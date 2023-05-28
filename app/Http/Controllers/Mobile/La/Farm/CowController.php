<?php

namespace App\Http\Controllers\Mobile\La\Farm;

use App\Http\Controllers\Controller;
use App\Models\Cows;
use App\Models\Farms;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CowController extends Controller
{
    public function storeCowData(Request $request){

        $validator = Validator::make($request->all(), [
            'FarmID' => 'required|integer',
            'CowCode' => 'required',
            'CowType' => 'required',
            'Age' => 'required',
            'Color' => 'required',
            'MilkCap' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
            //Existing CowCode And farm Name check
            $farm =  farms::where('FarmID', $request->FarmID)->first();
            $existingFarmName =  $farm->FarmName;
            $existingCowCodeCheck = Cows::where('CowCode',$request->CowCode)->first();
            if(!empty($existingCowCodeCheck) && !empty($existingFarmName) ){
                return response()->json([
                    'status' => 'Something Went Wrong',
                    'message' => 'Cow Code And Farm Name Already Taken'
                ], 400);
            }
            else{
                //Data Insert
                try {
                DB::beginTransaction();
                $cow = new Cows();
                $cow->FarmID = $request->FarmID;
                $cow->CowCode = $request->CowCode;
                $cow->CowType = $request->CowType;
                $cow->Age = $request->Age;
                $cow->Color = $request->Color;
                $cow->MilkCap = $request->MilkCap;

                $cow->EntryBy = Auth::user()->UserID;
                $cow->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
                $cow->EntryIPAddress = DeviceService::get_client_ip();
                $cow->EntryDiviceState = DeviceService::getBrowser();
                $cow->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
                $cow->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
                $cow->save();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cow Created Successfully'
                ]);

            }catch (\Exception $exception) {
                    return response()->json([
                        'status' => 'Something Went Wrong',
                        'message' => $exception->getMessage() . '-' . $exception->getLine()
                    ], 500);
                }

        }
    }
}
