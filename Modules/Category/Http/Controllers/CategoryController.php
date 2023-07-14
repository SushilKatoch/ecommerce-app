<?php

namespace Modules\Category\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Category\Entities\categories;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
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
        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:50|unique:categories'
        ]);


        if ($validator->fails()) {
            $error = $validator->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
        try {
            $uuid =Str::uuid()->getHex();
            $input = $request->all();
            $input['uuid'] =$uuid ;
            $slug=Str::slug($request->name);
            $input['slug'] = $slug;
           
            $category = categories::create($input);
            $categoryId = $category->uuid;
            $categories = categories::select('uuid', 'name','slug' ,'deleted_at', 'created_at', 'updated_at')
                ->where('uuid', $categoryId)->first();
            return response()->json([
                 'data' => $categories
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    /**
     * Show the specified resource.
     * @param int $id
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
        if ($validatorAuth->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
            $category = categories::select(
                'uuid',
                'name',
                'deleted_at',
                'created_at',
                'updated_at'
            )->where('deleted_at','=',null)->get();
            return response()->json([
                'status' => '200', 'message' => "All Product Categories Images ", "data" => $category
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request, $uuid)
    {
        if ($request->header('Authtoken') == null) {
            return response()->json([
                'status' => '401', 'error' => "No auth token"
            ], 401);
        }
        $auth_token = $request->header('Authtoken');

        $auth = User::where('authToken', $auth_token)->first();
        if ($auth == null) {
            return response()->json([
                'status' => '401', 'error' => "Invalid Auth Token"
            ], 401);
        }
        if ($request->bearerToken() == null) {
            return response()->json([
                'status' => '401', 'error' => "No access token"
            ], 401);
        }
        if ($auth->access_token != $request->bearerToken()) {
            return response()->json([
                'status' => '401', 'error' => "Invalid Token"
            ], 401);
        } elseif (Auth::user() == null) {
            return response()->json([
                'status' => '401', 'error' => "Session Expired"
            ], 401);
        }
        try {
            $categoryExists = categories::where('uuid', $uuid)->where('deleted_at','=',null)->exists();
            if ($categoryExists) {
                $category = categories::where('uuid', $uuid)
                    ->select(
                        'uuid',
                        'name',
                        'deleted_at',
                        'created_at',
                        'updated_at'
                    )->where('deleted_at','=',null)->get();
                return response()->json([
                    'status' => '200', 'message' => "Category Found", "data" => $category
                ]);
            } else {
                return response()->json([
                    'status' => '404', 'message' => "Category Not Found "
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
        if ($request->header('Authtoken') == null) {
            return response()->json([
                'status' => '401', 'error' => "No auth token"
            ], 401);
        }
        $auth_token = $request->header('Authtoken');

        $auth = User::where('authToken', $auth_token)->first();
        if ($auth == null) {
            return response()->json([
                'status' => '401', 'error' => "Invalid Auth Token"
            ], 401);
        }
        if ($request->bearerToken() == null) {
            return response()->json([
                'status' => '401', 'error' => "No access token"
            ], 401);
        }
        if ($auth->access_token != $request->bearerToken()) {
            return response()->json([
                'status' => '401', 'error' => "Invalid Token"
            ], 401);
        } elseif (Auth::user() == null) {
            return response()->json([
                'status' => '401', 'error' => "Session Expired"
            ], 401);
        }
        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|min:2|max:50|unique:categories'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => '400', 'error' =>  $validator->errors(),
            ], 400);
        }
        try {
            $categoryExists = categories::where('uuid', $request->uuid)->exists();
            if ($categoryExists) {
                $input = $request->all();
                $category = categories::where('uuid', $request->uuid)->first();
                $input = $request->all();
                $input['slug'] = Str::slug($request->name);
                $product =  $category->update($input);
                $categoryUpdate = categories::select(
                    'uuid',
                    'name',
                    'slug',
                    'deleted_at',
                    'created_at',
                    'updated_at'
                )
                    ->where('uuid',  $category->uuid)->first();
                return response()->json([
                    'status' => '200', 'message' => "Category Updated", "data" => $categoryUpdate
                ]);
            } else {
                return response()->json([
                    'status' => '404', 'message' => "No Category Found "
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
        if ($request->header('Authtoken') == null) {
            return response()->json([
                'status' => '401', 'error' => "No auth token"
            ], 401);
        }
        $auth_token = $request->header('Authtoken');

        $auth = User::where('authToken', $auth_token)->first();
        if ($auth == null) {
            return response()->json([
                'status' => '401', 'error' => "Invalid Auth Token"
            ], 401);
        }
        if ($request->bearerToken() == null) {
            return response()->json([
                'status' => '401', 'error' => "No access token"
            ], 401);
        }
        if ($auth->access_token != $request->bearerToken()) {
            return response()->json([
                'status' => '401', 'error' => "Invalid Token"
            ], 401);
        } elseif (Auth::user() == null) {
            return response()->json([
                'status' => '401', 'error' => "Session Expired"
            ], 401);
        }
        try {
            $category = categories::where('uuid', $uuid);

            $categories = $category->delete();
            $categoryDelete = categories::withTrashed()->select(
                'uuid',
                'name',
                'slug',
                'deleted_at',
                'created_at',
                'updated_at'
            )->where('uuid',  $uuid)->first();

            return response()->json([
                'status' => '200', 'message' => "Category Deleted", "data" => $categoryDelete
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
        if ($request->header('Authtoken') == null) {
            return response()->json([
                'status' => '401', 'error' => "No auth token"
            ], 401);
        }
        $auth_token = $request->header('Authtoken');

        $auth = User::where('authToken', $auth_token)->first();
        if ($auth == null) {
            return response()->json([
                'status' => '401', 'error' => "Invalid Auth Token"
            ], 401);
        }
        if ($request->bearerToken() == null) {
            return response()->json([
                'status' => '401', 'error' => "No access token"
            ], 401);
        }
        if ($auth->access_token != $request->bearerToken()) {
            return response()->json([
                'status' => '401', 'error' => "Invalid Token"
            ], 401);
        } elseif (Auth::user() == null) {
            return response()->json([
                'status' => '401', 'error' => "Session Expired"
            ], 401);
        }

        try {
            $category = categories::onlyTrashed()->select(
                'uuid',
                'name',
                'slug',
                'deleted_at',
                'created_at',
                'updated_at'
            )->orderBy('deleted_at', 'desc')->paginate();
            return response()->json([
                'status' => '200', 'message' => "All Categories Trashed", "data" => $category
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
        if ($request->header('Authtoken') == null) {
            return response()->json([
                'status' => '401', 'error' => "No auth token"
            ], 401);
        }
        $auth_token = $request->header('Authtoken');

        $auth = User::where('authToken', $auth_token)->first();
        if ($auth == null) {
            return response()->json([
                'status' => '401', 'error' => "Invalid Auth Token"
            ], 401);
        }
        if ($request->bearerToken() == null) {
            return response()->json([
                'status' => '401', 'error' => "No access token"
            ], 401);
        }
        if ($auth->access_token != $request->bearerToken()) {
            return response()->json([
                'status' => '401', 'error' => "Invalid Token"
            ], 401);
        } elseif (Auth::user() == null) {
            return response()->json([
                'status' => '401', 'error' => "Session Expired"
            ], 401);
        }

        try{
            $category = categories::withTrashed()->where('uuid', '=', $uuid);
            $category->restore();
            $categoryRestore = categories::select(
                'uuid',
                'name',
                'imageDescription',
                'alt',
                'deleted_at',
                'created_at',
                'updated_at'
            )
                ->where('uuid',  $uuid)->first();
            return response()->json([
                'status' => '200', 'message' => "Product Category Images Restored", "data" => $categoryRestore
            ]);
        }catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
       
    }
}
