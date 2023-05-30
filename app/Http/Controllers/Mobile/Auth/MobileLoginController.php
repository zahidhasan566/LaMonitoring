<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Traits\SMS;


class MobileLoginController extends Controller
{
    use SMS;
    public function index(Request $request){
        $phone = $request->phone;
        $user = User::Where(['Mobile' =>$phone ,'Status' => 1])->first();
        if($user){
            $SixDigitRandomNumber = rand(100000,999999);
            $user->OtpCode = $SixDigitRandomNumber;
            $user->OtpVerification =0;
            $user->save();
            $smscontent = 'আপনার লগিনের জন্য ওটিপি কোডটি হলো- ' .$SixDigitRandomNumber ;
            $mobileno = $phone;
            $respons = $this->sendsms($ip = '192.168.100.213', $userid = 'motors', $password = 'Asdf1234', $smstext = urlencode($smscontent), $receipient = urlencode($mobileno));

            if($respons->message =='Success!'){
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Code Sent Successfully!'
                ], 200);
            }
            else{
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Failed to sent code!'
                ], 401);

            }

//            print_r($sendSMS);


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

        try {
            if($user){
                if ($phone && $token = JWTAuth::attempt(['Mobile' => $phone, 'password' => $user->RawPassword])) {
                    $user->OtpVerification = 1;
                    $user->save();
                    $userDetails = User::select('UserID','Name','Email','Mobile','NID','Address','RoleID','Status','OtpCode','OtpVerification')->Where(['Mobile' =>$phone,'otpCode' => $otpCode])->first();
                    return $this->respondWithToken($token,$userDetails);
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
        catch (\Exception $exception) {
            return response()->json([
                'status' => 'Something Went Wrong',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    protected function respondWithToken($token,$userDetails)
    {
//        dd($user);
        return response()->json([
            'access_token' => $token,
            'Data' => $userDetails,
        ]);
    }


}
