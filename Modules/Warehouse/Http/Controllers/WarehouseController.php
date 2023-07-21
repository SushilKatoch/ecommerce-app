<?php

namespace Modules\Warehouse\Http\Controllers;

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
use Modules\Warehouse\Entities\Warehouse;

class WarehouseController extends Controller
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
            'name'                => ['required', Rule::unique('warehouse', 'name')->where('authId', $userId)],
            'addressLine1'        => 'required',
            'addressLine2'        => 'required',
            'city'                => 'required',
            'contactPersonName'   => 'required',
            'gstNumber'           => 'nullable',
            'fssaiNumber'         => 'nullable',
            'isActive'            => 'nullable|max:50',
            'isPrimaryWarehouse'  => 'nullable|max:191',
            'mobileNumber'        => 'required',
            'state'               => 'required|max:60',
            'country'             => 'required|max:80',
            'pincode'             => 'required|max:8',
            'regionDelivery'      => 'nullable',
            'orderBy'             => 'nullable|numeric',
            'seoData'             => 'nullable',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
        try {
            $input = $request->all();

            $warehouse = Warehouse::where('authId', auth()->id())->count();
            // $input['slug'] = Str::slug($request->name);
            $input['uuid'] = Str::uuid()->getHex();
            $input['authId'] = Auth::user()->id;
            $input['orderBy'] = ++$warehouse;


            $warehouse = Warehouse::create($input);

            $warehouseData = Warehouse::where('authId', Auth::user()->id)
                ->where('uuid', $warehouse->uuid)->where('deleted_at', '=', null)
                ->select(
                    'uuid',
                    'name',
                    'addressLine1',
                    'addressLine2',
                    'city',
                    'contactPersonName',
                    'gstNumber',
                    'isActive',
                    'isPrimaryWarehouse',
                    'mobileNumber',
                    'state',
                    'country',
                    'pincode',
                    'regionDelivery',
                    'orderBy',
                    'deleted_at',
                    'created_at',
                    'updated_at'
                )
                ->first();
            return response()->json([
                'data' => $warehouseData
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
            $warehouse = Warehouse::where('authId', '=', Auth::user()->id)
                ->select(
                    'uuid',
                    'name',
                    'addressLine1',
                    'addressLine2',
                    'city',
                    'contactPersonName',
                    'gstNumber',
                    'isActive',
                    'isPrimaryWarehouse',
                    'mobileNumber',
                    'state',
                    'country',
                    'pincode',
                    'regionDelivery',
                    'orderBy',
                    'deleted_at',
                    'created_at',
                    'updated_at'
                )

                ->get();

            return response()->json([
                "data" => $warehouse
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
          
            $warehouseExists = Warehouse::where('authId', Auth::user()->id)->where('uuid', $uuid)->exists();

            if ($warehouseExists) {
                $warehouse = Warehouse::where('authId', Auth::user()->id)
                ->where('uuid', $uuid)->where('deleted_at', '=', null)
                ->select(
                    'uuid',
                    'name',
                    'addressLine1',
                    'addressLine2',
                    'city',
                    'contactPersonName',
                    'gstNumber',
                    'isActive',
                    'isPrimaryWarehouse',
                    'mobileNumber',
                    'state',
                    'country',
                    'pincode',
                    'regionDelivery',
                    'orderBy',
                    'deleted_at',
                    'created_at',
                    'updated_at'
                )
                  ->first();
                return response()->json([
                    "data" => $warehouse
                ]);
            } else {
                
                 $array = [ 'No Warehouse Data Found'];
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
          'name'                => ['nullable', Rule::unique('warehouse', 'name')->where('authId', $userId)],
            'addressLine1'        => 'nullable',
            'addressLine2'        => 'nullable',
            'city'                => 'nullable',
            'contactPersonName'   => 'nullable',
            'gstNumber'           => 'nullable',
            'fssaiNumber'         => 'nullable',
            'isActive'            => 'nullable|max:50',
            'isPrimaryWarehouse'  => 'nullable|max:191',
            'mobileNumber'        => 'nullable|numeric|min:10|max:14',
            'state'               => 'nullable|max:60',
            'country'             => 'nullable|max:80',
            'pincode'             => 'nullable|max:8',
            'regionDelivery'      => 'nullable',
            'orderBy'             => 'nullable|numeric',
            'seoData'             => 'nullable',
        ]);
        if ($validator->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
        try {
           
            $warehouseExists = Warehouse::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->exists();
            if ($warehouseExists) {
                $input = $request->all();
                $warehouse = Warehouse::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->first();
              
                $warehouseData =  $warehouse->update($input);
                 $warehouseUpdated = Warehouse::where('authId', Auth::user()->id)
                ->where('uuid', $request->uuid)
                ->where('deleted_at', '=', null)
                ->select(
                    'uuid',
                    'name',
                    'addressLine1',
                    'addressLine2',
                    'city',
                    'contactPersonName',
                    'gstNumber',
                    'isActive',
                    'isPrimaryWarehouse',
                    'mobileNumber',
                    'state',
                    'country',
                    'pincode',
                    'regionDelivery',
                    'orderBy',
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
            $warehouse = Warehouse::where('authId', Auth::user()->id)->where('uuid', '=', $uuid);

            $warehouseData = $warehouse->delete();
            $warehouseDelete = Warehouse::withTrashed()
            ->select(
                'uuid',
                'name',
                'addressLine1',
                'addressLine2',
                'city',
                'contactPersonName',
                'gstNumber',
                'isActive',
                'isPrimaryWarehouse',
                'mobileNumber',
                'state',
                'country',
                'pincode',
                'regionDelivery',
                'orderBy',
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

        $jwtToken = $request->header('Authorization');

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

            $warehouse = Warehouse::select(
                'uuid',
                'name',
                'addressLine1',
                'addressLine2',
                'city',
                'contactPersonName',
                'gstNumber',
                'isActive',
                'isPrimaryWarehouse',
                'mobileNumber',
                'state',
                'country',
                'pincode',
                'regionDelivery',
                'orderBy',
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
            $warehouse = Warehouse::withTrashed()->where('uuid', '=', $request->uuid);
            $warehouse->restore();
            $warehouseRestore = Warehouse::select(
                'uuid',
                'name',
                'addressLine1',
                'addressLine2',
                'city',
                'contactPersonName',
                'gstNumber',
                'isActive',
                'isPrimaryWarehouse',
                'mobileNumber',
                'state',
                'country',
                'pincode',
                'regionDelivery',
                'orderBy',
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
