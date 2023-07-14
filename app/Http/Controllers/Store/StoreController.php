<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StoreController extends Controller
{
    public function gstKyc(Request $request)
    {
        $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
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
            ['Authorization' => ['required', Rule::exists('users', 'access_token')]]
        );
     if ($validatorAccess->fails()) {
           $error = $validatorAccess->errors();
           $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        // validation
        $validator = Validator::make($request->all(), [
            'gstin' => 'required|max:15'
        ]);


        if ($validator->fails()) {
              $error = $validator->errors();
              $errors=collect($error)->flatten();
            return response()->json([
                 'error' =>  $errors,
            ], 400);
        }
        try {

            $user = User::find(Auth::user()->id);
            $gstin = $request->gstin;
            $user->update($request->all());

            return response()->json([
              "data"=>[
                "gstin" => $gstin
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function pickupAddressKyc(Request $request)
    {
        $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
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
            ['Authorization' => ['required', Rule::exists('users', 'access_token')]]
        );
        
          if ($validatorAccess->fails()) {
              $error = $validatorAccess->errors();
              $errors=collect($error)->flatten();
            return response()->json([
                
                'error' =>  $errors,
            ], 401);
        }
        // validation

        $validator = Validator::make($request->all(), [
            'room' => 'required',
            'street' => 'required',
            'landmark' => 'required',
            'pincode' => 'required',
            'city' => 'required',
            'state' => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $errors=collect($error)->flatten();
            return response()->json([
                 'error' => $errors
            ], 400);
        }
        try {
            $user = User::find(Auth::user()->id);

            $input['warehouseAddress']['primary'] = $request->all();
            $user->warehouseAddress = $input;
            $user->save();
            $input['warehouseAddress'] = $request->all();
            return response()->json([
             "data"=>[
                "warehouseAddress" => $input['warehouseAddress']
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function bankDetailsKyc(Request $request)
    {
        $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
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
            ['Authorization' => ['required', Rule::exists('users', 'access_token')]]
        );
        
          if ($validatorAccess->fails()) {
                $error = $validatorAccess->errors();
              $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        // validation
        $validator = Validator::make($request->all(), [
            'accountNumber' => 'required|max:24',
            'ifscCode' => 'required|max:8'
        ]);

        if ($validator->fails()) {
              $error = $validator->errors();
              $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
        try {
            $ifscCode = $request->ifscCode;

            $accountNumber = $request->accountNumber;
            $user = User::find(Auth::user()->id);

            $input['bankDetails']['primary'] = $request->all();
            $user->bankDetails = $input;
            $user->save();

            return response()->json([
              "data"=>[
                "ifscCode" => $ifscCode,
                "accountNumber" => $accountNumber
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function supplierDetailsKyc(Request $request)
    {
        $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
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
            ['Authorization' => ['required', Rule::exists('users', 'access_token')]]
        );
        
          if ($validatorAccess->fails()) {
                $error = $validatorAccess->errors();
              $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        // validation

        $validator = Validator::make($request->all(), [
            'storeName' => 'required',
            'fullName' => 'required'
        ]);

        if ($validator->fails()) {
              $error = $validator->errors();
              $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 400);
        }
        try {
            $storeName = $request->storeName;
            $fullName = $request->fullName;
            $user = User::find(Auth::user()->id);
            $user->update($request->all());

            return response()->json([
               "data"=>[
                "storeName" => $storeName,
                "fullName" => $fullName
                ]

            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function userCategory(Request $request)
    {
        $authToken = $request->header('Authtoken');

        $validatorAuth = Validator::make(
            ['Authtoken' => $authToken],
            ['Authtoken' => ['required', Rule::exists('users', 'authToken')]]
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
            ['Authorization' => ['required', Rule::exists('users', 'access_token')]]
        );
        
          if ($validatorAccess->fails()) {
                $error = $validatorAccess->errors();
                $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        // validation

        $validator = Validator::make($request->all(), [
            'userId' => 'required|min:10',
        ]);
        if ($validator->fails()) {
              $error = $validator->errors();
              $errors=collect($error)->flatten();
            return response()->json([
               'error' =>  $errors,
            ], 400);
        }
        try {
            $categoryId = $request->CategoryId;
            $user = User::find(Auth::user()->id);
            $user->categoryId = $categoryId;
            $user->save();

            return response()->json([
                "data"=>[
                "storeName" => $categoryId
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
