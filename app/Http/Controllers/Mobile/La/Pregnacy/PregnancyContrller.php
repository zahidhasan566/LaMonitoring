<?php

namespace App\Http\Controllers\Mobile\La\Pregnacy;

use App\Http\Controllers\Controller;
use App\Models\Cows;
use App\Models\Entries;
use App\Models\PregnancyInformation;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PregnancyContrller extends Controller
{
    public function getPregnancySupportingData(Request $request){
        try{
            $existingCow = Entries::select(
                'Farms.Owner',
                'Cows.CowCode',
                'Entries.SeedDate',
                'Entries.BullID',
                'Farms.FarmID',
                'Entries.EntryID',
                'Cows.CowID',
                )
                ->join('Cows','Cows.CowID', 'Entries.CowID')
                ->join('Farms','Farms.FarmID', 'Cows.FarmID')
                ->where('Cows.CowCode',$request->CowCode)
                ->where('Entries.EntryBy', Auth::user()->UserID)
                ->first();

            return response()->json([
                'status' => 'success',
                'data' =>$existingCow
            ]);
        }
        catch (\Exception $exception){
            return response()->json([
                'status' => 'Something Went Wrong',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }

    }

    public function storePregnancyData(Request $request){
        $validator = Validator::make($request->all(), [
            'EntryID' => 'required|integer',
            'BullID' => 'required|integer',
            'FarmID' => 'required|integer',
            'CowCode' => 'required',
            'Owner' => 'required',
            'PregnancyTestDate' => 'required|date',
            'PregnancyTestResult' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json(['message' => $validator->errors()], 400);
        }
        else{
            try{

                //store pregnancy data
                $storePregnancyData = new PregnancyInformation();
                $storePregnancyData->EntryID = $request->EntryID;
                $storePregnancyData->Owner = $request->Owner;
                $storePregnancyData->CowID = $request->CowID;
                $storePregnancyData->SeedDate = $request->SeedDate;
                $storePregnancyData->BullID = $request->BullID;
                $storePregnancyData->PregnancyTestDate = $request->PregnancyTestDate;
                $storePregnancyData->PregnancyTestResult = $request->PregnancyTestResult;
                if($request->PregnancyTestResult =='Y' ){
                $storePregnancyData->PossibleDeliveryDate = $request->PossibleDeliveryDate;
                }
                $storePregnancyData->EntryBy = Auth::user()->UserID;
                $storePregnancyData->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
                $storePregnancyData->EntryIPAddress = DeviceService::get_client_ip();
                $storePregnancyData->EntryDiviceState = DeviceService::getBrowser();
                $storePregnancyData->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
                $storePregnancyData->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
                $storePregnancyData->save();

                return response()->json([
                    'status' => 'success',
                    'message' =>'Pregnancy Data Added Successfully'
                ]);
            }
            catch (\Exception $exception){
                return response()->json([
                    'status' => 'Something Went Wrong',
                    'message' => $exception->getMessage() . '-' . $exception->getLine()
                ], 500);
            }
        }
    }

    //GET ALL DATA
    public function getAllPregnancyData(){
        try{
            $existingData = PregnancyInformation::select('PregnancyInformation.*')->where('PregnancyInformation.EntryBy', Auth::user()->UserID)->get();
            return response()->json([
                'status' => 'success',
                'data' =>$existingData
            ]);
        }
        catch (\Exception $exception){
            return response()->json([
                'status' => 'Something Went Wrong',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }
}
