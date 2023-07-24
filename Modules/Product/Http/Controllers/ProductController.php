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
use Modules\Product\Entities\Product;

class ProductController extends Controller
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
            'productName'              => ['required', Rule::unique('products', 'name')->where('authId', $userId)],
            // 'slug'              => 'nullable|max:191|unique:product_categories',
            'productSkuCode'            => 'nullable',
            'productCategory'           => 'nullable',
            'productCondition'          => 'nullable',
            'productDescription'        => 'nullable',
            'productPrice'              => 'nullable',
            'productSellingPrice'       => 'nullable',
            'productBrand'              => 'nullable',
            'productAttributes'         => 'nullable',
            'productPrice'              => 'nullable',
            'imagesId'                  => 'nullable',
            'productCategoryId'         => 'nullable',
            'productUnit'               => 'nullable',
            'unit'                      => 'nullable',
            'productQuantity'           => 'nullable',
            'productWeight'             => 'nullable',
            'weightUnit'                => 'nullable',
            'shipmentWeight'            => 'nullable',
            'hsnCode'                   => 'nullable',
            'gstRate'                   => 'nullable',
            'inStock'                   => 'nullable',
            'isActive'                  => 'nullable|max:50',
            'isTaxable'                 => 'nullable',
            'variantId'                 => 'nullable',
            'tags'                      => 'nullable',
            'storeUuid'                 => 'nullable|numeric',
            'countryOfOrigin'           => 'nullable',
            'manufacturingAddress'      => 'nullable',
            'seoData'                   => 'nullable',
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

            $productOrder = Product::where('authId', auth()->id())->count();
            $input['slug'] = Str::slug($request->name);
            $input['storeUuid'] = Str::uuid()->getHex();
            $input['authId'] = Auth::user()->id;
            $input['orderBy'] = ++$productOrder;

            if ($request->categoryImageId == null) {
                $input['categoryImageId'] = '6fe520f76a014a3a9f9671be8a766012';
            }
            if ($request->bannerImageId == null) {
                $input['bannerImageId'] = '5832142d44ff4caab59f8253e9fdecd9';
            }

            $product = Product::create($input);

            $productCategory = Product::where('authId', Auth::user()->id)
                ->where('uuid', $product->uuid)->where('deleted_at', '=', null)
               
                ->first();
            return response()->json([
                'data' => $productCategory
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
            $product = Product::where('authId', '=', Auth::user()->id)
               

                ->get();

            return response()->json([
                "data" => $product
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

            $productExists = Product::where('authId', Auth::user()->id)->where('uuid', $uuid)->exists();

            if ($productExists) {
                $product = Product::where('authId', Auth::user()->id)
                    ->where('uuid', $uuid)->where('deleted_at', '=', null)
                    ->first();
                return response()->json([
                    "data" => $product
                ]);
            } else {

                $array = ['No Product Found'];
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
            'productName'              => ['required', Rule::unique('products', 'name')->where('authId', $userId)],
            // 'slug'              => 'nullable|max:191|unique:product_categories',
            'productSkuCode'            => 'nullable',
            'productCategory'           => 'nullable',
            'productCondition'          => 'nullable',
            'productDescription'        => 'nullable',
            'productPrice'              => 'nullable',
            'productSellingPrice'       => 'nullable',
            'productBrand'              => 'nullable',
            'productAttributes'         => 'nullable',
            'productPrice'              => 'nullable',
            'imagesId'                  => 'nullable',
            'productCategoryId'         => 'nullable',
            'productUnit'               => 'nullable',
            'unit'                      => 'nullable',
            'productQuantity'           => 'nullable',
            'productWeight'             => 'nullable',
            'weightUnit'                => 'nullable',
            'shipmentWeight'            => 'nullable',
            'hsnCode'                   => 'nullable',
            'gstRate'                   => 'nullable',
            'inStock'                   => 'nullable',
            'isActive'                  => 'nullable|max:50',
            'isTaxable'                 => 'nullable',
            'variantId'                 => 'nullable',
            'tags'                      => 'nullable',
            'storeUuid'                 => 'nullable|numeric',
            'countryOfOrigin'           => 'nullable',
            'manufacturingAddress'      => 'nullable',
            'seoData'                   => 'nullable',
        ]);
        if ($validator->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
        try {
            $productExists = Product::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->exists();
            if ($productExists) {
                $input = $request->all();
                $product = Product::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->first();
                if ($request->name) {
                    $input['slug'] = Str::slug($request->name);
                }

                $product =  $product->update($input);
                $productUpdated = Product::where('authId', Auth::user()->id)
                    ->where('uuid', $product->uuid)
                    ->where('deleted_at', '=', null)
                    ->with('')
                    ->with('banner')->first();
                return response()->json([
                    "data" => $productUpdated
                ]);
            } else {

                $array = ['No Product Found'];
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
            $product = Product::where('authId', Auth::user()->id)->where('uuid', '=', $uuid);

            $product = $product->delete();
            $productDelete = Product::withTrashed()->select('uuid', 'name', 'slug', 'isActive', 'imagesId', 'bannerImageId', 'deleted_at', 'created_at', 'updated_at')
                ->where('authId', Auth::user()->id)->where('uuid',  $uuid)->first();
            return response()->json([
                "data" => $productDelete
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

            $product = Product::select('uuid', 'name', 'slug', 'isActive', 'imagesId', 'bannerImageId', 'deleted_at', 'created_at', 'updated_at')
                ->where('authId', Auth::user()->id)->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();
            return response()->json([
                "data" => $product
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
            $product = Product::withTrashed()->where('uuid', '=', $request->uuid);
            $product->restore();
            $productRestore = Product::select('uuid', 'name', 'slug', 'isActive', 'imagesId', 'bannerImageId', 'deleted_at', 'created_at', 'updated_at')
                ->where('authId', Auth::user()->id)->where('uuid',  $uuid)->first();
            return response()->json([
                "data" => $productRestore
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function swapProductOrder(Request $request)
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
                'status' => '400', 'error' =>  $errors,
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
            ], 400);
        }
        try {

            $productExists = Product::where('authId', Auth::user()->id)->exists();

            if ($productExists) {

                $column1Id = $request->input('column1Id');
                $column2Id = $request->input('column2Id');
  
                $column1 = Product::where('uuid', $column1Id)->first();
                $column2 = Product::where('uuid', $column2Id)->first();

                $column1Order = $column1->orderBy;
                $column2Order = $column2->orderBy;

                $column1->orderBy = $column2Order;
                $column2->orderBy = $column1Order;

                $column1->save();
                $column2->save();
                return response(['data' => ['message'=>'Order has been swapped']],200);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function active(Request $request, $uuid)
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
                'status' => '400', 'error' =>  $errors,
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
            ], 400);
        }
        try {

            $productExists = Product::where('authId', Auth::user()->id)->exists();

            if ($productExists) {
                $product = Product::where('authId', Auth::user()->id)
                    ->where('uuid', $uuid)
                    ->first();


                if ($product->isActive == true) {

                    $product->isActive = 0;
                    $product->save();
                } else {
                    $product->isActive = 1;
                    $product->save();
                }
                $products = Product::where('authId', Auth::user()->id)
                    ->where('uuid', $uuid)
                    ->where('uuid', $uuid)->where('deleted_at', '=', null)
                  
                    ->first();

                return response()->json([
                    "data" => $products
                ]);
            } else {

                $array = ['No Product Found'];
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
}
