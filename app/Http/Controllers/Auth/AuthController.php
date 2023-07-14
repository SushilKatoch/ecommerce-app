<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Exception;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendOtpEmail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
   

    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:120',
        ]);

        if ($validator->fails()) {
              $error = $validator->errors();
              $errors=collect($error)->flatten();
            return response()->json([
               'error' => $errors,
            ], 400);
        }
        try {
            $otp = rand(1000, 9999);
            $input = $request->all();
            $input['otp'] = $otp;
            $input['otpExpiresIn'] = Carbon::now()->addMinutes(10);
            $token = Str::random(32);
            $input['token'] = $token;
            $user = User::where('email', $request->input('email'))->exists();

            if ($user == false) {
                $store = User::create($input);
            } else {
                $user = User::where('email', $request->input('email'))->first();

                $user->otp =  $otp;
                $user->token = $token;
                $user->otpExpiresIn = !empty($request->otpExpiresIn) ? $request->otpExpiresIn : $user->otpExpiresIn;
                $user->save();
            }

            // send otp in the email

            $otpDetail = 'Your OTP is : ' . $otp;
            $email = $request->email;



            Mail::send('emails.otpEmail', ['email' => $email, 'otp' => $otpDetail], function ($message) use ($email, $otpDetail) {
                $message->to($email);
                $message->subject('Otp Verification');
            });

            return response(['data' => ['token'=>$token]],200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        $otp = $request->otp;
        $token = $request->token;
        // Validate the request
        $validator = Validator::make(
           [
               'otp'=>$otp,
               'token'=>$token
               ],
               [
                   'otp'=>[
                       'required',Rule::exists('users','otp')
                       ],
                    'token'=>[
                        'required'
                        ],
                    
                ]
                      
        );

        if ($validator->fails()) {
              $error = $validator->errors();
              $errors=collect($error)->flatten();
            return response([
                'error' =>  $errors,
            ], 400);
        }

        try {
            $user = User::where('token', $request->input('token'))->first();

            // Verify OTP
            if ($user->otp != $request->input('otp')) {
                return response()->json([
                    'error' => 'Invalid OTP.'
                ], 400);
            }

            $token = Auth::guard('api')->login($user);
            $auth_token = Str::random(32);
            // Clear the OTP field in the user model
            $user->otp = null;
            $user->access_token = $token;
            $user->authToken = $auth_token;
            $user->save();
            if ($user->emailVerified == null) {
                $user->emailVerified = "true";

                $user->save();
                // Return JWT token
                return response()->json([
                    "data"=>[
                        "user" => "New User",
                    "token" => $token,
                    "authToken" => $auth_token
                        ]
                ]);
            } else {

                // Return JWT token
                return response()->json([
                   "data"=>[
                    "user" => "Existing User",
                    "token" => $token,
                    "auth_token" => $auth_token
                    ]
                ]);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
        // Generate JWT token

    }

    /**
     * Logout user (Invalidate the token).
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
       $user = User::find(Auth::user()->id);
       $user->authToken =null;
        $user->save();
        auth()->logout();
        
        return response()->json(['data'=>[ 'message' => 'User logged out successfully']]);
    }

    public function getUser(Request $request)
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
            $errors=collect($error)->flatten();
            return response()->json([
                'error' =>  $errors,
            ], 401);
        }
        try {
            $user =User::select(
                'fullName',
                'storeAddress',
                'warehouseAddress',
                'gstin',
                'storeImage',
                'email',
                'mobileNumber'
            )->where('id', Auth::user()->id)->first();
            return response([
                
                'data' => $user
            ],200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function verifyToken($token)
    {   
        try{
        if (User::where('token', '=', $token)->exists()) {
            $user = User::where('token', '=', $token)->first();
            $email = $user->email;
            return response([
                'data'=>[
                    'isValid' => true,
                'email' => $email
                    ]
                
            ],200);
        } else {
            return response([
                'data' =>[
                      'isValid' => false,
                    ]
              
            ],400);
        }
        }  catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
