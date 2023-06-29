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
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login', 'register']]);
    // }

    public function userEmailOtp(Request $request)
    {


        $otp = rand(1000, 9999);
        $input = $request->all();
        $input['otp']= $otp;
        $input['otpExpiresIn'] =Carbon::now()->addMinutes(10);

        $store = Store::create($input);

        // send otp in the email

        $otpDetail = 'Your OTP is : ' . $otp;
        $email = $request->email;

        try {

            Mail::send('emails.otpEmail', ['email' => $email, 'otp' => $otpDetail], function ($message) use ($email, $otpDetail) {
                $message->to($email);
                $message->subject('Otp Verification');
            });
           
            return response(["status" => 200, "message" => "OTP sent successfully"]);
        } catch (Exception $e) {
            echo $e->getMessage();

            return response(["status" => 401, 'message' => 'Invalid']);
        }
    }


    public function register(Request $request)
    {
        $store = Store::where('email',$request->email)->first();
       dd(Carbon::now());
        if($store->otp == $request->otp)
        {
            $store->verified = 'yes';
            $store->otp = null;
            $store->save();
        }else{
            return response(["status" => 401, 'message' => 'Wrong Otp Entered']);
        }

    }


    public function storeDetail(Request $request)
    {
        // validation
        $request->validate([
            'storeName' => 'required',
            'fullName' => 'required',
            'gst' => 'required',
            'warehouseAddress' => 'required',
            'ifscCode' => 'required',
            'account_number' => 'required',
        ]);
        $store = Store::find(Auth::user()->id);
        $store = $store->update($request->all());

        return response()->json(["status" => 200, "message" => "User Store registered successfully"]);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'status' => 200,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function login(Request $request)
    {
        $input = $request->only('email');
        $jwt_token = null;

        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => 200,
                'message' => 'Invalid Email or Password',
            ], Response::HTTP_UNAUTHORIZED);
        }
  
        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);
    }

      /**
     * Refresh a JWT token
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
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
}
