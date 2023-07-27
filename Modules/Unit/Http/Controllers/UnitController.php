<?php

namespace Modules\Unit\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Modules\Unit\Entities\Unit;

class UnitController extends Controller
{

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
        );

        if ($validatorAuth->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

        $jwtToken = $request->bearerToken();

        $validatorAccess = Validator::make(
            [
                'Authorization' => $jwtToken
            ],
            [
                'Authorization' => ['required', Rule::exists('users', 'access_token')]
            ]
        );
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        $userId = auth()->id();
        // validation
        $validator = Validator::make($request->all(), [
            'unitName'         => ['required', Rule::unique('units', 'unitName')->where('authId', $userId)],
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
        try {
       
            $unit = Unit::where('authId', auth()->id())->count();
            $input['uuid'] = Str::uuid()->getHex();
            $input['authId'] = Auth::user()->id;

            $unit = Unit::create($input);

            $unitData = Unit::where('authId', Auth::user()->id)
                ->where('uuid', $unit->uuid)->where('deleted_at', '=', null)
                ->select(
                    'uuid',
                    'unitName',
                    'deleted_at',
                    'created_at',
                    'updated_at'
                )
                ->first();
            return response()->json([
                'data' => $unitData
            ]);
        } catch (ValidationException $e) {
            throw new \Exception('Validation failed: ' . $e->getMessage(), 422);
        }
    }

    /**
     * Show all the specified resource.
     * @return Renderable
     */
    public function showAll(Request $request)
    {
        $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
        );

        if ($validatorAuth->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

        $jwtToken = $request->bearerToken();

        $validatorAccess = Validator::make(
            ['Authorization' => $jwtToken],
            ['Authorization' => ['required', Rule::exists('users', 'access_token')]]
        );
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
            $unit = Unit::where('authId', '=', Auth::user()->id)
            ->select(
                'uuid',
                'unitName',
                'deleted_at',
                'created_at',
                'updated_at'
            )->get();

            return response()->json([
                "data" => $unit
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            $array = ['Something went wrong'];
            return response(['error' => $array], 500);
        }
    }

     /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request, $uuid)
    {
        $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
        );

        if ($validatorAuth->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

        $jwtToken = $request->bearerToken();

        $validatorAccess = Validator::make(
            ['Authorization' => $jwtToken],
            ['Authorization' => ['required', Rule::exists('users', 'access_token')]]
        );
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
          
            $unitExists = Unit::where('authId', Auth::user()->id)->where('uuid', $uuid)->exists();

            if ($unitExists) {
                $unit = Unit::where('authId', Auth::user()->id)
                ->where('uuid', $uuid)->where('deleted_at', '=', null)
                ->select(
                    'uuid',
                    'unitName',
                    'deleted_at',
                    'created_at',
                    'updated_at'
                )->first();
                return response()->json([
                    "data" => $unit
                ]);
            } else {
                
                 $array = [ 'No Unit Data Found'];
                return response([
                    'error'=>$array
                ], 404);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
             return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

     /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request)
    {
        $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
        );

        if ($validatorAuth->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $validatorAuth->errors(),
            ], 401);
        }
        $jwtToken = $request->bearerToken();

        $validatorAccess = Validator::make(
            ['Authorization' => $jwtToken],
            ['Authorization' => ['required', Rule::exists('users', 'access_token')]]
        );
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        $userId = auth()->id();
        // validation
        $validator = Validator::make($request->all(), [
          'unitName'                => ['nullable', Rule::unique('units', 'unitName')->where('authId', $userId)],
        ]);
        if ($validator->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
        try {
           
            $warehouseExists = Unit::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->exists();
            if ($warehouseExists) {
                $input = $request->all();
                $warehouse = Unit::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->first();
              
                $warehouseData =  $warehouse->update($input);
                 $warehouseUpdated = Unit::where('authId', Auth::user()->id)
                ->where('uuid', $request->uuid)
                ->where('deleted_at', '=', null)
                ->select(
                    'uuid',
                    'unitName',
                    'deleted_at',
                    'created_at',
                    'updated_at'
                )
               ->first();
                return response()->json([
                    "data" => $warehouseUpdated
                ]);
            } else {
                 
                 $array = [ 'No Product Category Found'];
                return response([
                    'error'=>$array
                ], 404);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $uuid)
    {
        $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
        );

        if ($validatorAuth->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

        $jwtToken = $request->bearerToken();

        $validatorAccess = Validator::make(
            ['Authorization' => $jwtToken],
            ['Authorization' => ['required', Rule::exists('users', 'access_token')]]
        );
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
            $warehouse = Unit::where('authId', Auth::user()->id)->where('uuid', '=', $uuid);

            $warehouseData = $warehouse->delete();
            $warehouseDelete = Unit::withTrashed()
            ->select(
                'uuid',
                'unitName',
                'deleted_at',
                'created_at',
                'updated_at'
            )
                ->where('authId', Auth::user()->id)->where('uuid',  $uuid)->first();
            return response()->json([
               "data" => $warehouseDelete
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
           return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    /**
     * List of trashed ertries
     * works if the softdelete is enabled.
     *
     * @return Response
     */
    public function trashed(Request $request)
    {
          $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
        );

        if ($validatorAuth->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

        $jwtToken = $request->bearerToken();

        $validatorAccess = Validator::make(
            ['Authorization' => $jwtToken],
            ['Authorization' => ['required', Rule::exists('users', 'access_token')]]
        );
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {

            $warehouse = Unit::select(
                'uuid',
                'unitName',
                'deleted_at',
                'created_at',
                'updated_at'
            )
                ->where('authId', Auth::user()->id)->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();
            return response()->json([
                "data" => $warehouse
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
           return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    /**
     * Restore a soft deleted entry.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function restore(Request $request, $uuid)
    {
        $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
        );

        if ($validatorAuth->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

        $jwtToken = $request->bearerToken();

        $validatorAccess = Validator::make(
            ['Authorization' => $jwtToken],
            ['Authorization' => ['required', Rule::exists('users', 'access_token')]]
        );
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
            $warehouse = Unit::withTrashed()->where('uuid', '=', $request->uuid);
            $warehouse->restore();
            $warehouseRestore = Unit::select(
                'uuid',
                'unitName',
                'deleted_at',
                'created_at',
                'updated_at'
            )
                ->where('authId', Auth::user()->id)->where('uuid',  $uuid)->first();
            return response()->json([
                "data" => $warehouseRestore
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
