<?php

namespace Modules\Product\Http\Controllers;

use App\Models\productCategory;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\productCategories;
use Modules\Product\Http\Requests\productCategoriesRequest;
use Illuminate\Support\Str;
use Modules\Product\Entities\productCategoriesBannerImages;
use Illuminate\Validation\ValidationException;
use Modules\Product\Entities\productCategoriesImages;

class ProductCategoriesController extends Controller
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
            'name'              => ['required', Rule::unique('product_categories', 'name')->where('authId', $userId)],
            // 'slug'              => 'nullable|max:191|unique:product_categories',
            'categoryImageId'            => 'nullable',
            'bannerImageId'        => 'nullable',
            'description'           => 'nullable',
            'bannerImage'              => 'nullable',
            'bannerImageMobile'       => 'nullable',
            'isActive'  => 'nullable|max:50',
            'parentId'    => 'nullable|max:191',
            'parentName'              => 'nullable',
            'reorder'             => 'nullable|numeric',
            'seoData'            => 'nullable',
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

            $productOrder =productCategories::where('authId',auth()->id())->count();
            $input['slug'] = Str::slug($request->name);
            $input['uuid'] = Str::uuid()->getHex();
            $input['authId'] = Auth::user()->id;
            $input['orderBy'] = ++$productOrder;
         
            if($request->categoryImageId == null){
                $input['categoryImageId'] ='6fe520f76a014a3a9f9671be8a766012';
            }
            if($request->bannerImageId == null){
                $input['bannerImageId'] ='5832142d44ff4caab59f8253e9fdecd9';
            }

            $product = productCategories::create($input);

            $productCategory = productCategories::where('product_categories.authId', Auth::user()->id)
                ->where('product_categories.uuid', $product->uuid)->where('deleted_at', '=', null)
                ->with('category')
                ->with('banner')->first();
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
                  $productCategory = productCategories::where('product_categories.authId','=' ,Auth::user()->id)
                  ->with('category')
                  ->with('banner')
                
                  ->get();
                
            return response()->json([
                "data" => $productCategory
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
              $array = [ 'Something went wrong'];
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
          
            $productCategoryExists = productCategories::where('product_categories.authId', Auth::user()->id)->where('uuid', $uuid)->exists();

            if ($productCategoryExists) {
                $productCategory = productCategories::where('product_categories.authId', Auth::user()->id)
                ->where('product_categories.uuid', $uuid)->where('deleted_at', '=', null)
                ->with('category')
                  ->with('banner')->first();
                return response()->json([
                    "data" => $productCategory
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
            'name'              => ['required', Rule::unique('product_categories', 'name')->where('authId', $userId)],
            //  'slug'              => ['required', Rule::unique('product_categories', 'slug')->where('authId',$userId)],
            'categoryImageId'            => 'nullable',
            'bannerImageId'        => 'nullable',
            'description'           => 'nullable',
            'bannerImage'              => 'nullable',
            'bannerImageMobile'       => 'nullable',
            'isActive'  => 'nullable|max:50',
            'parentId'    => 'nullable|max:191',
            'parentName'              => 'nullable',
            'reorder'             => 'nullable|numeric',
            'seoData'            => 'nullable',
        ]);
        if ($validator->fails()) {
            $error = $validatorAuth->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
        try {
            $productCategoryExists = productCategories::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->exists();
            if ($productCategoryExists) {
                $input = $request->all();
                $productCategory = productCategories::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->first();
                if ($request->name) {
                    $input['slug'] = Str::slug($request->name);
                }

                $product =  $productCategory->update($input);
                 $productCategoryUpdated = productCategories::where('product_categories.authId', Auth::user()->id)
                ->where('product_categories.uuid', $product->uuid)
                ->where('deleted_at', '=', null)
                ->with('category')
                ->with('banner')->first();
                return response()->json([
                    "data" => $productCategoryUpdated
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
            $productCategory = productCategories::where('authId', Auth::user()->id)->where('uuid', '=', $uuid);

            $product = $productCategory->delete();
            $productCategoryDelete = productCategories::withTrashed()->select('uuid', 'name', 'slug', 'isActive', 'imagesId', 'bannerImageId', 'deleted_at', 'created_at', 'updated_at')
                ->where('authId', Auth::user()->id)->where('uuid',  $uuid)->first();
            return response()->json([
               "data" => $productCategoryDelete
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

            $productCategory = productCategories::select('uuid', 'name', 'slug', 'isActive', 'imagesId', 'bannerImageId', 'deleted_at', 'created_at', 'updated_at')
                ->where('authId', Auth::user()->id)->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();
            return response()->json([
                "data" => $productCategory
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
            $productCategory = productCategories::withTrashed()->where('uuid', '=', $request->uuid);
            $productCategory->restore();
            $productCategoryRestore = productCategories::select('uuid', 'name', 'slug', 'isActive', 'imagesId', 'bannerImageId', 'deleted_at', 'created_at', 'updated_at')
                ->where('authId', Auth::user()->id)->where('uuid',  $uuid)->first();
            return response()->json([
                "data" => $productCategoryRestore
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function swapProductCategoryOrder(Request $request)
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

            $productCategoryExists = productCategories::where('authId', Auth::user()->id)->exists();

            if ($productCategoryExists) {

                $column1Id = $request->input('column1Id');
                $column2Id = $request->input('column2Id');
  
                $column1 = productCategories::where('uuid', $column1Id)->first();
                $column2 = productCategories::where('uuid', $column2Id)->first();

                $column1Order = $column1->orderBy;
                $column2Order = $column2->orderBy;

                $column1->orderBy = $column2Order;
                $column2->orderBy = $column1Order;

                $column1->save();
                $column2->save();
                return response()->json([
                    'message' => "Product Category Order Swapped"
                ]);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
