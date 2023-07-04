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


class AuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login', 'register']]);
    // }

    public function requestOtp(Request $request)
    {


        $otp = rand(1000, 9999);
        $input = $request->all();
        $input['otp'] = $otp;
        $input['otpExpiresIn'] = Carbon::now()->addMinutes(10);
        $token =Str::random(32);
        $input['token'] = $token;
        $user = User::where('email', $request->input('email'))->exists();

        if ($user == false) {
            $store = User::create($input);
        } else {
            $user = User::where('email', $request->input('email'))->first();

            $user->otp =  $otp;
            $user->token = Hash::make($token);
            $user->otpExpiresIn = !empty($request->otpExpiresIn) ? $request->otpExpiresIn : $user->otpExpiresIn;
            $user->save();
        }

        // send otp in the email

        $otpDetail = 'Your OTP is : ' . $otp;
        $email = $request->email;

        try {

            Mail::send('emails.otpEmail', ['email' => $email, 'otp' => $otpDetail], function ($message) use ($email, $otpDetail) {
                $message->to($email);
                $message->subject('Otp Verification');
            });

            return response(["status" => 200, "message" => "OTP sent successfully",'token' => $token]);
        } catch (Exception $e) {
            echo $e->getMessage();

            return response(["status" => 401, 'message' => 'Invalid']);
        }
    }

    public function verifyOTP(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
            'token'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
     
        $user = User::where('email', $request->input('email'))->first();
       
        if (!$user) {
            return response()->json(['error' => 'Store not found.'], 404);
        }
      
        // Verify OTP
        if ($user->otp != $request->input('otp')) {
            return response()->json(['error' => 'Invalid OTP.'], 401);
        }elseif(!Hash::check($request->input('token'), $user->token))
        {
            return response()->json(['error' => 'Invalid Token.'], 401);
        }

        $token = Auth::guard('api')->login($user);

        // Clear the OTP field in the user model
        $user->otp = null;
        $user->save();
        if ($user->emailVerified == null) {
            $user->emailVerified = "true";
            $user->save();
        // Return JWT token
        return response()->json([
            "status" => 200,
            "message" => "User Store Logged In Successfully",
            "user" =>"New User",
            "token" => $token
        ]);
        }else{
  // Return JWT token
  return response()->json([
    "status" => 200,
    "message" => "User Store Logged In Successfully",
    "user" =>"Existing User",
    "token" => $token
]);
        }
        // Generate JWT token
      
    }

    /**
     * Logout user (Invalidate the token).
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {

        auth()->logout();
        return response()->json(['status' => 'success', 'message' => 'User logged out successfully']);
    }

    public function getUser()
    {
        $user = User::findorFail(Auth::user()->id);
        return response()->json([
            'status' => 'success',
            'message' => 'User fetched successfully',
            'data' => $user
            ]);
    }
}
