<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class MobileLoginController extends Controller
{
    public function index(Request $request){
        $phone = $request->phone;
        $user = User::Where(['Mobile' =>$phone ,'Status' => 1])->first();
        if($user){
            $SixDigitRandomNumber = rand(100000,999999);
            $user->OtpCode = $SixDigitRandomNumber;
            $user->OtpVerification =0;
            $user->save();
            return response()->json([
                'status' => 'Success',
                'message' => 'Code Sent Successfully!'
            ], 200);

        }
        else{
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid User Phone Number!'
            ], 401);
        }
    }

    //Otp Verification
    public function otpVerification(Request $request){
        $phone = $request->phone;
        $otpCode = $request->otpCode;
        $user = User::Where(['Mobile' =>$phone,'otpCode' => $otpCode])->first();

        if($user){
            if ($phone && $token = JWTAuth::attempt(['Mobile' => $phone, 'password' => $user->RawPassword])) {
                $user->OtpVerification = 1;
                $user->save();
                return $this->respondWithToken($token,$user);
            }
            return response()->json([
                'status' => 'Success',
                'message' => 'Code Sent Successfully!'
            ], 200);

        }
        else{
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Otp Code!'
            ], 401);
        }
    }

    protected function respondWithToken($token,$user)
    {
//        dd($user);
        return response()->json([
            'access_token' => $token,
            'Users' => $user,
            'token_type' => 'bearer',
        ]);
    }


}
