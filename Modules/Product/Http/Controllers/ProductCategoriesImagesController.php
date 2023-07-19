<?php

namespace Modules\Product\Http\Controllers;

use App\Models\productCategory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Entities\productCategoriesImages;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\productCategoriesBannerImages;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProductCategoriesImagesController extends Controller
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
        ['Authtoken' => ['required',Rule::exists('users','authToken')]]
    );

     if ($validatorAuth->fails()) {
          $error = $validatorAuth->errors();
          $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

     $jwtToken = $request->bearerToken();
     
    $validatorAccess = Validator::make(
        ['Authorization' => $jwtToken],
        ['Authorization' => ['required',Rule::exists('users','access_token')]]
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
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048'
            ]);


             if ($validator->fails()) {
          $error = $validator->errors();
          $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
            try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                 $extension = $image->getClientOriginalExtension();
                 $imageName =$request->name. Str::random(6). '.'.$extension;
                 $imageNameExists= $request->name. '.'.$extension;
                $exists = productCategoriesImages::where('authId',auth()->id())->where('name','=',$request->name)->exists();
                
                $imageFullPath = 'imageUploads/' . $imageName;

                $imagepath=$image->storeAs('imageUploads/',$imageName);
                if($exists){
                     $name = $request->name;
                    $processedName = Str::replace(' ', '_', $name);
                  $imageName =$processedName. Str::random(6). '.'.$extension;

                $imageFullPath = 'imageUploads/' . $imageName;

                $imagepath=$image->storeAs('imageUploads/',$imageName);
                }else{
                    $name = $request->name;
                    $processedName = Str::replace(' ', '_', $name);
                    $imageName= $processedName. '.'.$extension;
                    $imageFullPath = 'imageUploads/' . $imageName;

                    $imagepath=$image->storeAs('imageUploads/',$imageName);
                }
            }
            $productCategoryImages = new productCategoriesImages();
            // $productCategoryImages->name = $imageName;
            $productCategoryImages->name = $request->name;
            $productCategoryImages->path = $imageFullPath;
            $productCategoryImages->alt = $request->alt;
            $productCategoryImages->authId = Auth::user()->id;
            $productCategoryImages->uuid = Str::uuid()->getHex();
            $productCategoryImages->imageDescription = $request->imageDescription;
            $productCategoryImages->save();
            $productCategoryId = $productCategoryImages->uuid;
            $productCategory = productCategoriesImages::select('uuid', 'name', 'path', 'alt', 'imageDescription', 'deleted_at', 'created_at', 'updated_at')
                ->where('authId', Auth::user()->id)->where('uuid', $productCategoryId)->first();
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
        ['Authtoken' => ['required',Rule::exists('users','authToken')]]
    );

     if ($validatorAuth->fails()) {
          $error = $validatorAuth->errors();
          $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

     $jwtToken = $request->bearerToken();

    $validatorAccess = Validator::make(
        ['Authorization' => $jwtToken],
        ['Authorization' => ['required',Rule::exists('users','access_token')]]
    );
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
            $productCategory = productCategoriesImages::where('authId', Auth::user()->id)->where('deleted_at', '=', null)->select(
                'uuid',
                'name',
                'imageDescription',
                'path',
                'alt',
                'deleted_at',
                'created_at',
                'updated_at'
            )->orderBy('created_at', 'desc')->get();
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
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request, $uuid)
    {
            $authToken = $request->header('Authtoken');

    $validatorAuth = Validator::make(
        ['Authtoken' => $authToken],
        ['Authtoken' => ['required',Rule::exists('users','authToken')]]
    );

      if ($validatorAuth->fails()) {
          $error = $validatorAuth->errors();
          $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

     $jwtToken = $request->bearerToken();

    $validatorAccess = Validator::make(
        ['Authorization' => $jwtToken],
        ['Authorization' => ['required',Rule::exists('users','access_token')]]
    );
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
            $productCategoryExists = productCategoriesImages::where('authId', Auth::user()->id)->where('deleted_at', '=', null)
                ->where('uuid', $uuid)->exists();
            if ($productCategoryExists) {
                $productCategory = productCategoriesImages::where('authId', Auth::user()->id)->where('uuid', $uuid)
                    ->select(
                        'uuid',
                        'name',
                        'path',
                        'imageDescription',
                        'alt',
                        'deleted_at',
                        'created_at',
                        'updated_at'
                    )->first();
                return response()->json([
                   "data" => $productCategory
                ]);
            } else {
               
                $array = [ 'No Product Category Image Found'];
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
        ['Authtoken' => ['required',Rule::exists('users','authToken')]]
    );

     if ($validatorAuth->fails()) {
          $error = $validatorAuth->errors();
          $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

     $jwtToken = $request->bearerToken();

    $validatorAccess = Validator::make(
        ['Authorization' => $jwtToken],
        ['Authorization' => ['required',Rule::exists('users','access_token')]]
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
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
        ]);


        if ($validator->fails()) {
          $error = $validator->errors();
          $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
        try {
            $productCategoryExists = productCategoriesImages::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->exists();
            if ($productCategoryExists) {
                $input = $request->all();
                $productCategory = productCategoriesImages::where('authId', Auth::user()->id)->where('uuid', $request->uuid)->first();
                $productName = $productCategory->name;
                $productPath = $productCategory->path;
                if ($request->hasFile('image')) {
                     if (File::exists($productPath)) {
                        File::delete($productPath);
                    }
                   $image = $request->file('image');
                   $extension = $image->getClientOriginalExtension();
                 $imageName =$request->name. Str::random(6). '.'.$extension;

                $imageFullPath = 'imageUploads/' . $imageName;

                $imagepath=$image->storeAs('imageUploads/',$imageName);
                  
                    $productPath = $imageFullPath;
                }
                $input['name'] = $productName;
             
              
               if($request->imageName)
                {
                     $imageName =$request->name. Str::random(6). '.'.$extension;
                    $oldPath ='/home/swadeshdigital/ecommerce_swadeshdigital_com/storage/app/' . $productPath;
                    $newPath = '/home/swadeshdigital/ecommerce_swadeshdigital_com/storage/app/imageUploads/' . $imageName;
                  
                    $nPath = 'imageUploads/' . $imageName;
                    File::move($oldPath,$newPath);
                   
                }
                $input['path'] = $nPath;
                

                $product =  $productCategory->update($input);
                $productCategoryUpdate = productCategoriesImages::select(
                    'uuid',
                    'name',
                    'path',
                    'imageDescription',
                    'alt',
                    'deleted_at',
                    'created_at',
                    'updated_at'
                )
                    ->where('authId', Auth::user()->id)->where('uuid',  $productCategory->uuid)->first();
                return response()->json([
                   "data" => $productCategoryUpdate
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
        ['Authtoken' => ['required',Rule::exists('users','authToken')]]
    );

     if ($validatorAuth->fails()) {
          $error = $validatorAuth->errors();
          $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

     $jwtToken = $request->bearerToken();

    $validatorAccess = Validator::make(
        ['Authorization' => $jwtToken],
        ['Authorization' => ['required',Rule::exists('users','access_token')]]
    );
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
            $productCategory = productCategoriesImages::where('authId', Auth::user()->id)->where('uuid', $uuid);

            $product = $productCategory->delete();
            $productCategoryDelete = productCategoriesImages::withTrashed()->select(
                'uuid',
                'name',
                'path',
                'imageDescription',
                'alt',
                'deleted_at',
                'created_at',
                'updated_at'
            )
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
        ['Authtoken' => ['required',Rule::exists('users','authToken')]]
    );

      if ($validatorAuth->fails()) {
          $error = $validatorAuth->errors();
          $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

     $jwtToken = $request->bearerToken();

    $validatorAccess = Validator::make(
        ['Authorization' => $jwtToken],
        ['Authorization' => ['required',Rule::exists('users','access_token')]]
    );
        if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
            $productCategory = productCategoriesImages::onlyTrashed()->select(
                'uuid',
                'name',
                'path',
                'imageDescription',
                'alt',
                'deleted_at',
                'created_at',
                'updated_at'
            )->where('authId', Auth::user()->id)->orderBy('deleted_at', 'desc')->paginate();
            return response()->json([
                'status' => '200', 'message' => "All Product Categories Images Trashed", "data" => $productCategory
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
        ['Authtoken' => ['required',Rule::exists('users','authToken')]]
    );

      if ($validatorAuth->fails()) {
          $error = $validatorAuth->errors();
          $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }

      $jwtToken = $request->bearerToken();

    $validatorAccess = Validator::make(
        ['Authorization' => $jwtToken],
        ['Authorization' => ['required',Rule::exists('users','access_token')]]
    );
         if ($validatorAccess->fails()) {
            $error = $validatorAccess->errors();
            $errors = collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
            $productCategory = productCategoriesImages::withTrashed()->where('authId', Auth::user()->id)->where('uuid', '=', $uuid);
            $productCategory->restore();
            $productCategoryRestore = productCategoriesImages::select(
                'uuid',
                'name',
                'path',
                'imageDescription',
                'alt',
                'deleted_at',
                'created_at',
                'updated_at'
            )
                ->where('authId', Auth::user()->id)->where('uuid',  $uuid)->first();
            return response()->json([
                'status' => '200', 'message' => "Product Category Images Restored", "data" => $productCategoryRestore
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
