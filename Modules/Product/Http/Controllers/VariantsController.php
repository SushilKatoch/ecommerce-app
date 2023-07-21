<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Product\Entities\Variants;

class VariantsController extends Controller
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
            'skuCode'          => 'nullable',
            'attributes'       => 'nullable',
            'dimensions'           => 'nullable',
            'inventory'            => 'nullable',
            'productPrice'      => 'nullable',
            'inStock'  => 'nullable|max:50',
            'productSellingPrice'  => 'nullable|max:191',
            'imagesId'            => 'nullable',
            'weight'            => 'nullable|numeric',
            'weightUnit'            => 'nullable',
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

            $variant = Variants::where('authId', auth()->id())->count();
            $input['uuid'] = Str::uuid()->getHex();
            $input['authId'] = Auth::user()->id;

            if ($request->categoryImageId == null) {
                $input['categoryImageId'] = '6fe520f76a014a3a9f9671be8a766012';
            }
            if ($request->bannerImageId == null) {
                $input['bannerImageId'] = '5832142d44ff4caab59f8253e9fdecd9';
            }

            $variant = Variants::create($input);

            $variantData = Variants::where('product_categories.authId', Auth::user()->id)
                ->where('product_categories.uuid', $variant->uuid)->where('deleted_at', '=', null)
                ->select(
                    'uuid',
                    'skuCode',
                    'attributes',
                    'dimensions',
                    'inventory',
                    'productPrice',
                    'productSellingPrice',
                    'productBrand',
                    'imagesId',
                    'weight',
                    'weightUnit',
                    'inStock',
                    'deleted_at',
                    'created_at',
                    'updated_at'
                )
                ->first();
            return response()->json([
                'data' => $variantData
            ]);
        } catch (ValidationException $e) {
            throw new \Exception('Validation failed: ' . $e->getMessage(), 422);
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
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
            $variant = Variants::where('product_categories.authId', '=', Auth::user()->id)
            ->select(
                'uuid',
                'skuCode',
                'attributes',
                'dimensions',
                'inventory',
                'productPrice',
                'productSellingPrice',
                'productBrand',
                'imagesId',
                'weight',
                'weightUnit',
                'inStock',
                'deleted_at',
                'created_at',
                'updated_at'
            )
            ->get();

            return response()->json([
                "data" => $variant
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

            $variantExists = Variants::where('product_categories.authId', Auth::user()->id)->where('uuid', $uuid)->exists();

            if ($variantExists) {
                $variant = Variants::where('product_categories.authId', Auth::user()->id)
                    ->where('product_categories.uuid', $uuid)->where('deleted_at', '=', null)
                    ->select(
                        'uuid',
                        'skuCode',
                        'attributes',
                        'dimensions',
                        'inventory',
                        'productPrice',
                        'productSellingPrice',
                        'productBrand',
                        'imagesId',
                        'weight',
                        'weightUnit',
                        'inStock',
                        'deleted_at',
                        'created_at',
                        'updated_at'
                    )
                    ->first();
                return response()->json([
                    "data" => $variant
                ]);
            } else {

                $array = ['No Product Category Found'];
                return response([
                    'error' => $array
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
            'skuCode'          => 'nullable',
            'attributes'       => 'nullable',
            'dimensions'           => 'nullable',
            'inventory'            => 'nullable',
            'productPrice'      => 'nullable',
            'inStock'  => 'nullable|max:50',
            'productSellingPrice'  => 'nullable|max:191',
            'imagesId'            => 'nullable',
            'weight'            => 'nullable|numeric',
            'weightUnit'            => 'nullable',
        ]);
        if ($validator->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
        try {
            $variantExists = Variants::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->exists();
            if ($variantExists) {
                $input = $request->all();
                $variant = Variants::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->first();
                if ($request->name) {
                    $input['slug'] = Str::slug($request->name);
                }

                $variantData =  $variant->update($input);
                $variantUpdated = Variants::where('product_categories.authId', Auth::user()->id)
                    ->where('product_categories.uuid', $variantData->uuid)
                    ->where('deleted_at', '=', null)
                    ->select(
                        'uuid',
                        'skuCode',
                        'attributes',
                        'dimensions',
                        'inventory',
                        'productPrice',
                        'productSellingPrice',
                        'productBrand',
                        'imagesId',
                        'weight',
                        'weightUnit',
                        'inStock',
                        'deleted_at',
                        'created_at',
                        'updated_at'
                        )
                    ->first();
                return response()->json([
                    "data" => $variantUpdated
                ]);
            } else {

                $array = ['No Product Category Found'];
                return response([
                    'error' => $array
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
            $variant = Variants::where('authId', Auth::user()->id)->where('uuid', '=', $uuid);

            $variantData = $variant->delete();
            $variantDelete = Variants::withTrashed()
            ->select(
                'uuid',
                'skuCode',
                'attributes',
                'dimensions',
                'inventory',
                'productPrice',
                'productSellingPrice',
                'productBrand',
                'imagesId',
                'weight',
                'weightUnit',
                'inStock',
                'deleted_at',
                'created_at',
                'updated_at'
            )
                ->where('authId', Auth::user()->id)->where('uuid',  $uuid)->first();
            return response()->json([
                "data" => $variantDelete
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

            $variantData = Variants::
            select(
                'uuid',
                'skuCode',
                'attributes',
                'dimensions',
                'inventory',
                'productPrice',
                'productSellingPrice',
                'productBrand',
                'imagesId',
                'weight',
                'weightUnit',
                'inStock',
                'deleted_at',
                'created_at',
                'updated_at'
            )->where('authId', Auth::user()->id)->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();
            return response()->json([
                "data" => $variantData
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
            $variantData = Variants::withTrashed()->where('uuid', '=', $request->uuid);
            $variantData->restore();
            $variantRestore = Variants::select('uuid', 'name', 'slug', 'isActive', 'imagesId', 'bannerImageId', 'deleted_at', 'created_at', 'updated_at')
                ->where('authId', Auth::user()->id)->where('uuid',  $uuid)->first();
            return response()->json([
                "data" => $variantRestore
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

  
}
