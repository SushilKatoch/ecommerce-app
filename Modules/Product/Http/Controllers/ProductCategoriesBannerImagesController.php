<?php

namespace Modules\Product\Http\Controllers;

use App\Models\productCategory;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Entities\productCategoriesBannerImages;
use Modules\Product\Http\Requests\productCategoriesRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class ProductCategoriesBannerImagesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        try {
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
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'status' => '400', 'error' => "Empty Image"
                ], 400);
            }
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $path = $image->move(public_path('productCategory/image'), $imageName);
            }
            $productCategoryImages = new productCategoriesBannerImages();
            $productCategoryImages->name = $imageName;
            $productCategoryImages->path = $path;
            $productCategoryImages->authId = Auth::user()->id;
            $productCategoryImages->alt = $request->alt;
            $productCategoryImages->uuid = Str::uuid()->getHex();
            $productCategoryImages->imageDescription = $request->imageDescription;
            $productCategoryImages->save();
            $productCategoryId = $productCategoryImages->uuid;
            $productCategory = productCategoriesBannerImages::select('uuid', 'name', 'slug', 'isActive', 'imagesId', 'bannerImageId', 'deleted_at', 'created_at', 'updated_at')
                ->where('authId', Auth::user()->id)->where('uuid', $productCategoryId)->first();

            return response()->json([
                'status' => '200', 'message' => "Product Category Created",'data'=>$productCategory
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

        $productCategory = productCategoriesBannerImages::select(
            'uuid',
            'name',
            'imageDescription',
            'alt'
        )->where('authId', Auth::user()->id)->get();
        return response()->json([
            'status' => '200', 'message' => "All Product Categories Images ", "data" => $productCategory
        ]);
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

        $productCategoryExists = productCategoriesBannerImages::where('uuid', $uuid)->exists();
        if ($productCategoryExists) {
            $productCategory = productCategoriesBannerImages::where('authId', Auth::user()->id)->where('uuid', $uuid)
                ->select(
                    'uuid',
                    'name',
                    'imageDescription',
                    'alt'
                )->get();
            return response()->json([
                'status' => '200', 'message' => "Product Category Images Found", "data" => $productCategory
            ]);
        } else {
            return response()->json([
                'status' => '404', 'message' => "No Product Category Images Found "
            ], 404);
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
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => '400', 'error' => "Empty Image"
            ], 400);
        }
        $productCategoryExists = productCategoriesBannerImages::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->exists();
        if ($productCategoryExists) {
            $input = $request->all();
            $productCategory = productCategoriesBannerImages::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->first();
            $productName = $productCategory->name;
            $productPath = $productCategory->path;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $path = $image->move(public_path('productCategory/image'), $imageName);
                $productName = $imageName;
                $productPath = $path;
            }
            $input['name'] = $productName;
            $input['path'] = $productPath;

            $product =  $productCategory->update($input);
            $productCategoryUpdate = productCategoriesBannerImages::select('uuid', 'name', 'slug', 'isActive', 'imagesId', 'bannerImageId', 'deleted_at', 'created_at', 'updated_at')
            ->where('authId', Auth::user()->id)->where('uuid',  $productCategory->uuid)->first();

            return response()->json([
                'status' => '200', 'message' => "Product Category Images Updated", "data" => $productCategoryUpdate
            ]);
        } else {
            return response()->json([
                'status' => '404', 'message' => "No Product Category Images Found "
            ], 404);
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

        $productCategory = productCategoriesBannerImages::where('authId', Auth::user()->id)->where('uuid', $uuid);

        $product = $productCategory->delete();
        $productCategoryDelete = productCategoriesBannerImages::withTrashed()->select('uuid', 'name', 'slug', 'isActive', 'imagesId', 'bannerImageId', 'deleted_at', 'created_at', 'updated_at')
            ->where('authId', Auth::user()->id)->where('uuid',  $uuid)->first();
        return response()->json([
            'status' => '200', 'message' => "Product Category Images Deleted", "data" => $productCategoryDelete
        ]);
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


        $productCategory = productCategoriesBannerImages::onlyTrashed()->where('authId', Auth::user()->id)->orderBy('deleted_at', 'desc')->paginate();
        return response()->json([
            'status' => '200', 'message' => "All Product Categories Images Trashed", "data" => $productCategory
        ]);
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
        $productCategory = productCategoriesBannerImages::withTrashed()->where('authId', Auth::user()->id)->where('uuid', '=', $uuid);
        $productCategory->restore();
        $productCategoryRestore = productCategoriesBannerImages::select('uuid', 'name', 'slug', 'isActive', 'imagesId', 'bannerImageId', 'deleted_at', 'created_at', 'updated_at')
        ->where('authId', Auth::user()->id)->where('uuid',  $uuid)->first();
        return response()->json([
            'status' => '200', 'message' => "Product Category Images Restored", "data" => $productCategoryRestore
        ]);
    }
}
