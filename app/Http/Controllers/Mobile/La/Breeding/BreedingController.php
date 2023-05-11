<?php

namespace App\Http\Controllers\Mobile\La\Breeding;

use App\Http\Controllers\Controller;
use App\Models\Bulls;
use App\Models\Cows;
use App\Models\Entries;
use App\Models\Farms;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BreedingController extends Controller
{

    public function getBullData(){
        $bullData=  Bulls::select('bulls.*','bullTypes.*')->join('bullTypes','bullTypes.BullTypeID','bulls.BullTypeID')->get();
        return response()->json([
            'status' => 'success',
            '$allBullData' => $bullData
        ], 200);
    }
    public function storeBreedingData(Request $request){

        $validator = Validator::make($request->all(), [
            'CowID' => 'required|integer',
            'FarmID' => 'required|integer',
            'Age' => 'required',
            'MilkCap' => 'required',
            'HotDate' => 'required|date',
            'SeedDate' => 'required|date',
            'TestDate' => 'required|date',
            'BirthDate' => 'required|date',
            'BullID' => 'required|integer',
            'BullTypeID' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        //Data Insert
        try {
            DB::beginTransaction();
            $breeding = new Entries();
            $breeding->CowID = $request->CowID;
            $breeding->FarmID = $request->FarmID;
            $breeding->Age = $request->Age;
            $breeding->MilkCap = $request->MilkCap;
            $breeding->HotDate = $request->HotDate;
            $breeding->SeedDate = $request->SeedDate;
            $breeding->TestDate = $request->TestDate;
            $breeding->BirthDate = $request->BirthDate;
            $breeding->BullID = $request->BullID;
            $breeding->BullTypeID = $request->BullTypeID;


            $breeding->EntryBy = Auth::user()->UserID;
            $breeding->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
            $breeding->EntryIPAddress = DeviceService::get_client_ip();
            $breeding->EntryDiviceState = DeviceService::getBrowser();
            $breeding->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $breeding->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $breeding->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Breeding Created Successfully'
            ]);

        }catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }
    public function getBreedingData(Request $request){
        $CowID = $request->CowID;
        $FarmID = $request->FarmID;
        $BullID = $request->BullID;

        //get existing Breeding
        $allBreedingInfo = Entries::select('Entries.*','Farms.*','Cows.*')->with('LaInfo')->join('Farms','Farms.FarmID','Entries.FarmID')->join('Cows','Cows.CowID','Entries.CowID')->get();

        //Search Option
        if(!empty($CowID)){
            $allBreedingInfo = $allBreedingInfo->where('CowID',$CowID);
        }
        if(!empty($FarmID)){
            $allBreedingInfo = $allBreedingInfo->where('FarmID',$FarmID);
        }
        if(!empty($BullID)){
            $allBreedingInfo = $allBreedingInfo->where('BullID',$request->$BullID);
        }
        return response()->json([
            'status' => 'success',
            '$allBreedingInfo' => $allBreedingInfo
        ], 200);


    }

}
