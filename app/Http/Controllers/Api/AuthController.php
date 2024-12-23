<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserVerify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //Validate data
        // $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages()
            ], 200);
        }

        //Request is valid, create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token_mail = Str::random(64);

        UserVerify::create([
            'user_id' => $user->id,
            'token' => $token_mail,
            'expired_email' => Carbon::now()->addHour(1),
        ]);
        Mail::send('email.emailVerificationEmail', ['token' => $token_mail], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Email Verification Mail');
        });

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages()
            ], 200);
        }

        //Request is validated
        //Crean token
        try {
            $expired = Carbon::now()->addHours(6)->timestamp;
            if (!$token = JWTAuth::attempt($credentials, ['exp' =>  $expired])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
            if (!Auth::user()->is_email_verified) {
                return  response()->json(['message' => 'You need to confirm your account. We have sent you an activation code, please check your email.'], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
                'error' => $e,
            ], 500);
        }


        //Token created, return with success response and jwt token
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'message' => 'Authorization successfully',
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
                'user' => $user,
                'expires_in' => $expired,
            ]
        ]);
    }
    public function verifyAccount($token)
    {
        //cek validasi verifikasi email
        $verifyUser = UserVerify::where('token', $token)->first();

        $message = 'Sorry your email cannot be identified.';
        $code = 400;

        if (!is_null($verifyUser)) {
            // $date1= $verifyUser->expired_email;
            // $date2 = Carbon::now();

            // $result = $date2 > $date1;
            // if($result == true){
            //     $message = "Token is Expired";
            //     $code = 400;
            //     }else{
            $user = User::find($verifyUser->user_id);

            if (!$user->is_email_verified) {
                $user->is_email_verified = 1;
                $user->save();
                $message = "Your e-mail is verified. You can now login.";
                $code = 200;
            } else {
                $message = "Your e-mail is already verified. You can now login.";
                $code = 200;
            }
            // }

        }

        return  response()->json([
            'message' => $message,
        ], $code);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json([
            'success' => true,
            'message' => 'User has been logged out'
        ], 200);
    }

    public function refresh()
    {
        return response()->json([
            'success' => true,
            'message' => 'Created new token Authorization successfully',
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
                'user' => Auth::user(),
                'expires_in' => Carbon::now()->addHour(1)->timestamp,
            ]
        ]);
    }
    public function user_profile(Request $request)
    {
        $user = Auth::user();
        return response()->json(['user' => $user]);
    }
}
