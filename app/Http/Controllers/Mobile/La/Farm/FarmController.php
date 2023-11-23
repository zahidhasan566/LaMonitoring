<?php

namespace App\Http\Controllers\Mobile\La\Farm;

use App\Http\Controllers\Controller;
use App\Models\Farms;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FarmController extends Controller
{
    public function getFarmInfo(Request $request){

        $search = $request->search;
        $FarmName = $request->FarmName;
        $Owner = $request->Owner;
        $RegistrationNumber = $request->RegistrationNumber;
        $Mobile = $request->Mobile;
        $Address = $request->Address;

        //get existing farms
        $allFarmInfo = Farms::select('Farms.*')->with('Cows','LaInfo:UserID,Name,Email,Mobile,NID,Address,RoleID,Status,OtpCode,OtpVerification')
            ->where(function ($q) use ($search) {
                $q->where('Farms.FarmName', 'like', '%' . $search . '%');
                $q->orWhere('Owner', 'like', '%' . $search . '%');
                $q->orWhere('RegistrationNumber', 'like', '%' . $search . '%');
                $q->orWhere('Mobile', 'like', '%' . $search . '%');
            })
        ;

//        //Search Option
//        if(!empty($FarmName)){
//            $allFarmInfo = $allFarmInfo->orWhere('Farms.FarmName', 'like', '%' . $search . '%');
//        }
//        if(!empty($Owner)){
//            $allFarmInfo = $allFarmInfo->orWhere('Owner', 'like', '%' . $search . '%');
//        }
//        if(!empty($RegistrationNumber)){
//            $allFarmInfo = $allFarmInfo->orWhere('RegistrationNumber','like', '%' . $search . '%');
//        }
//        if(!empty($Mobile)){
//            $allFarmInfo = $allFarmInfo->orWhere('Mobile','like', '%' . $search. '%');
//        }

        $allFarmInfo= $allFarmInfo->paginate(20);


        return response()->json([
            'status' => 'success',
            'Data' => $allFarmInfo
        ], 200);
    }
    public function getUserBasedFarmInfo(Request $request){
        $search = $request->search;
        $FarmName = $request->FarmName;
        $Owner = $request->Owner;
        $RegistrationNumber = $request->RegistrationNumber;
        $Mobile = $request->Mobile;
        $Address = $request->Address;

        //get existing farms
        $userBasedFarmInfo = Farms::select('Farms.*')->with('Cows','LaInfo:UserID,Name,Email,Mobile,NID,Address,RoleID,Status,OtpCode,OtpVerification')
            ->where('Farms.EntryBy',Auth::user()->Id)
        ;
        $userBasedFarmInfo= $userBasedFarmInfo->get();
        $totalFarm = $userBasedFarmInfo->count();


        return response()->json([
            'status' => 'success',
            'Data' =>$userBasedFarmInfo,
            'TotalFarm' =>$totalFarm,
        ], 200);
    }


    public function storeFarmData(Request $request){

        $validator = Validator::make($request->all(), [
            'FarmName' => 'required|string',
            'Owner' => 'required',
            'Address' => 'required',
            'RegistrationNumber' => 'required',
            'Mobile' => 'required|min:11|max:11'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        //Data Insert
        try {
            DB::beginTransaction();
            $farm = new Farms();
            $farm->FarmName = $request->FarmName;
            $farm->Owner = $request->Owner;
            $farm->RegistrationNumber = $request->RegistrationNumber;
            $farm->Mobile = $request->Mobile;
            $farm->Address = $request->Address;

            $farm->EntryBy = Auth::user()->Id;
            $farm->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
            $farm->EntryIPAddress = DeviceService::get_client_ip();
            $farm->EntryDiviceState = DeviceService::getBrowser();
            $farm->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $farm->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $farm->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'farm Created Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 200);
        }
    }
}
