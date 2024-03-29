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
                'Bulls.BullName',
                'Farms.FarmID',
                'Entries.EntryID',
                'Cows.CowID',
                )
                ->join('Cows','Cows.CowID', 'Entries.CowID')
                ->join('Farms','Farms.FarmID', 'Cows.FarmID')
                ->join('Bulls','Bulls.BullID', 'Entries.BullID')
                ->where('Cows.CowCode',$request->CowCode)
                ->where('Entries.EntryBy', Auth::user()->Id)
                ->OrderBy('Entries.EntryID','desc')
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
            'PregnancyTestDate' => 'required|date',
            'PregnancyTestResult' => 'required',
            'PossibleDeliveryDate' => 'required_if:PregnancyTestResult,==,Y',
        ]);

        if ($validator->fails()){
            return response()->json(['message' => $validator->errors()], 400);
        }
        else{
            $existingCow = Entries::select(
                'Farms.FarmID',
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
                ->join('Bulls','Bulls.BullID', 'Entries.BullID')
                ->where('Entries.CowID', $request->CowID)
                ->first();

            if(empty($existingCow)){
                return response()->json([
                    'status' => 'error',
                    'message' =>'Pregnancy Data Did Not Match By Any Cow'
                ]);
            }
            try{
                //store pregnancy data
                $storePregnancyData = new PregnancyInformation();
                $storePregnancyData->EntryID = $existingCow->EntryID;
                $storePregnancyData->Owner = $existingCow->Owner;
                $storePregnancyData->CowID = $existingCow->CowID;
                $storePregnancyData->SeedDate =$existingCow->SeedDate;
                $storePregnancyData->BullID = $existingCow->BullID;
                $storePregnancyData->PregnancyTestDate = $request->PregnancyTestDate;
                $storePregnancyData->PregnancyTestResult = $request->PregnancyTestResult;
                if($request->PregnancyTestResult =='Y' ){
                $storePregnancyData->PossibleDeliveryDate = $request->PossibleDeliveryDate;
                }
                $storePregnancyData->EntryBy = Auth::user()->Id;
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
            $existingData = PregnancyInformation::select(
                'PregnancyInformation.PregnancyID',
                'PregnancyInformation.EntryID',
                'PregnancyInformation.Owner',
                'PregnancyInformation.CowID',
                'PregnancyInformation.SeedDate',
                'PregnancyInformation.BullID',
                'Cows.CowCode',
                'Bulls.BullName',
                'PregnancyInformation.PregnancyTestDate',
                'PregnancyInformation.PregnancyTestResult',
                'PregnancyInformation.PossibleDeliveryDate',

            )
                 ->join('Cows','Cows.CowID', 'PregnancyInformation.CowID')
                ->join('Bulls','Bulls.BullID', 'PregnancyInformation.BullID')
                ->where('PregnancyInformation.EntryBy', Auth::user()->Id)->get();
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
