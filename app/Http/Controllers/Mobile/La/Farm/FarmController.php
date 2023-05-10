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

        $FarmName = $request->FarmName;
        $Owner = $request->Owner;
        $RegistrationNumber = $request->RegistrationNumber;
        $Mobile = $request->Mobile;
        $Address = $request->Address;

        //get existing farms
        $allFarmInfo = Farms::select('Farms.*')->with('Cows','LaInfo');

        //Search Option
        if(!empty($FarmName)){
            $allFarmInfo = $allFarmInfo->where('FarmName',$FarmName);
        }
        if(!empty($Owner)){
            $allFarmInfo = $allFarmInfo->where('Owner',$Owner);
        }
        if(!empty($RegistrationNumber)){
            $allFarmInfo = $allFarmInfo->where('RegistrationNumber',$request->RegistrationNumber);
        }
        if(!empty($Mobile)){
            $allFarmInfo = $allFarmInfo->where('Mobile',$Mobile);
        }
        if(!empty($Address)){
            $allFarmInfo = $allFarmInfo->where('Address',$Address);
        }

        $allFarmInfo= $allFarmInfo->paginate(20);


        return response()->json([
            'status' => 'success',
            '$allFarmInfo' => $allFarmInfo
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

            $farm->EntryBy = Auth::user()->UserID;
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
