<?php

namespace App\Http\Controllers\Mobile\La\Breeding;

use App\Http\Controllers\Controller;
use App\Models\Bulls;
use App\Models\BullTypes;
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
        try {
            $bullData=  Bulls::select('bulls.*','bullTypes.*')->join('bullTypes','bullTypes.BullTypeID','bulls.BullTypeID')->get();
            return response()->json([
                'status' => 'success',
                'Data' => $bullData
            ], 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                'status' => 'Something Went Wrong',
                'message' => $exception->getMessage()
            ], 500);
        }

    }

    //get Bull Type data
    public function getBullTypeData(){
        try {
            $bulltypeData=  BullTypes::select('bullTypes.*')->get();
            return response()->json([
                'status' => 'success',
                'Data' => $bulltypeData
            ], 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                'status' => 'Something Went Wrong',
                'message' => $exception->getMessage()
            ], 500);
        }
    }
    public function storeBreedingData(Request $request){

        $sixmonthsOldDate = date("Y-m-d H:i:s", strtotime("-6 months"));


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
        //Existing CowCode And farm Name check
        $existingBreeding =  Entries::select('Entries.*')->where('FarmID', $request->FarmID)->where('CowID',$request->CowID)->whereDate('EntryDate', '>=', $sixmonthsOldDate)->OrderBy('EntryID', 'desc')->first();

        if($existingBreeding ){
            return response()->json([
                'status' => 'Something Went Wrong',
                'message' => 'Cow Code And Farm Name Already Taken In Less Than 6 months'
            ], 400);
        }
        else{
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
                ],200);

            }catch (\Exception $exception) {
                return response()->json([
                    'status' => 'Something Went Wrong',
                    'message' => $exception->getMessage() . '-' . $exception->getLine()
                ], 500);
            }
        }

    }
    public function getBreedingData(Request $request){
        $CowID = $request->CowID;
        $FarmID = $request->FarmID;
        $BullID = $request->BullID;
        $CurrentUser = Auth::user()->UserID;

        //get existing Breeding
//        $allBreedingInfo = Entries::select('Entries.SeedDate','Farms.FarmName','Cows.CowCode',
//            DB::raw("COUNT(ReEntries.EntryID)"))
//            ->join('ReEntries','ReEntries.EntryID','Entries.EntryID')
//            ->join('Farms','Farms.FarmID','Entries.FarmID')
//            ->join('Cows','Cows.CowID','Entries.CowID')
//            ->where('Entries.EntryBy',Auth::user()->UserID)
//            ->where('ReEntries.EntryID','Entries.EntryID')
//            ->get();

        $allBreedingInfo = DB::select("select e.EntryID,e.SeedDate,e.MilkCap,c.CowID,c.CowType,c.Age,c.Color,f.FarmID,c.CowCode,f.FarmName,
       (select COUNT(EntryID) from ReEntries re where re.EntryID = e.EntryID) as NumberOfReBreed
       from Entries e
        join Farms f on f.FarmID = e.FarmID
         join Cows c on c.CowID = e.CowID
        where e.EntryBy = '$CurrentUser'
        ");
//     dd($allBreedingInfo)      ;
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
        try {
            return response()->json([
                'status' => 'success',
                'Data' => $allBreedingInfo
            ], 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                'status' => 'Something Went Wrong',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }

    }

}
