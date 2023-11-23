<?php

namespace App\Http\Controllers\Mobile\La\Breeding;

use App\Http\Controllers\Controller;
use App\Models\Entries;
use App\Models\ReEntries;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReBreedingController extends Controller
{
    public function getReBreedingData(Request $request){
        try {
            $CowID = $request->CowID;
            $FarmID = $request->FarmID;

            //get existing Breeding
            $allReBreedingInfo = ReEntries::join('Farms','Farms.FarmID','ReEntries.FarmID')
                ->join('Cows','Cows.CowID','ReEntries.CowID')
                ->select('EntryID','Farms.FarmName','Cows.CowCode','ReEntries.SeedDate',DB::raw("COUNT(EntryID) as ReBreedCount"))
                ->where('ReEntries.EntryBy',Auth::user()->Id)
                ->groupBy('EntryID','Farms.FarmName','Cows.CowCode','ReEntries.SeedDate')
                ->get();
            $totalReBreeding = count($allReBreedingInfo);

            //Search Option
            if(!empty($CowID)){
                $allReBreedingInfo = $allReBreedingInfo->where('CowID',$CowID);
            }
            if(!empty($FarmID)){
                $allReBreedingInfo = $allReBreedingInfo->where('FarmID',$FarmID);
            }
            return response()->json([
                'status' => 'success',
                'Data' => $allReBreedingInfo,
                'TotalReBreeding' => $totalReBreeding,
            ], 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                'status' => 'Something Went Wrong',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }

    }

    //Store Re Breeding data
    public function storeReBreedingData(Request $request){

        $validator = Validator::make($request->all(), [
            'EntryID' => 'required|integer',
            'CowID' => 'required|integer',
            'FarmID' => 'required|integer',
            'SeedDate' => 'required|date',
            'TestDate' => 'required|date',
            'BirthDate' => 'required|date',
            'BullID' => 'required|integer',
            'BullTypeID' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        //Existing CowCode And farm Name check
        $existingReBreeding =  ReEntries::select('ReEntries.*')->where('FarmID', $request->FarmID)->where('CowID',$request->CowID)->OrderBy('ReID', 'desc')->get();
        if(count($existingReBreeding)>=3){
            return response()->json([
                'status' => 'Something Went Wrong',
                'message' => 'Cow Code And Farm Name Already Taken Three Times'
            ], 400);
        }


        else{
            try {
                //Existing Breeding Info
                $existingBreedingInfo = Entries::where('EntryID',$request->EntryID)->first();
                if($existingBreedingInfo){
                    DB::beginTransaction();
                    $rebreeding = new ReEntries();
                    $rebreeding->EntryID = $request->EntryID;
                    $rebreeding->CowID = $request->CowID;
                    $rebreeding->FarmID = $request->FarmID;
                    $rebreeding->SeedDate = $request->SeedDate;
                    $rebreeding->TestDate = $request->TestDate;
                    $rebreeding->BirthDate = $request->BirthDate;
                    $rebreeding->BullID = $request->BullID;
                    $rebreeding->BullTypeID = $request->BullTypeID;
                    $rebreeding->Comments = $request->Comments;

                    $rebreeding->EntryBy = Auth::user()->Id;
                    $rebreeding->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
                    $rebreeding->EntryIPAddress = DeviceService::get_client_ip();
                    $rebreeding->EntryDiviceState = DeviceService::getBrowser();
                    $rebreeding->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
                    $rebreeding->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
                    $rebreeding->save();
                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => ' Re Breeding Created Successfully'
                    ]);
                }

            }catch (\Exception $exception) {
                return response()->json([
                    'status' => 'Something Went Wrong',
                    'message' => $exception->getMessage() . '-' . $exception->getLine()
                ], 500);
            }
        }

        //Data Insert

    }
}
