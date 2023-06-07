<?php

namespace App\Http\Controllers\Mobile\La\Culf;

use App\Http\Controllers\Controller;
use App\Models\CulfInformation;
use App\Models\Entries;
use App\Models\PregnancyInformation;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CulfController extends Controller
{

    public function getCulfSupportingData(Request $request){
    try{
        $existingCow = Entries::select(
            'Cows.CowCode',
            'Entries.BullID',
            'Bulls.BullName'
        )
            ->join('Cows','Cows.CowID', 'Entries.CowID')
            ->join('Farms','Farms.FarmID', 'Cows.FarmID')
            ->join('Bulls','Bulls.BullID', 'Entries.BullID')
            ->where('Entries.EntryBy', Auth::user()->UserID)
            ->OrderBy('Entries.EntryID','desc')
            ->get();

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
    public function storeCulfData( Request  $request){

        $validator = Validator::make($request->all(), [
            'CulfCode' => 'required',
            'CowCode' => 'required',
            'BullID' => 'required',
            'CulfBirthDate' => 'required|date',
            'Color' => 'required',
            'Gender' => 'required',
            'BirthWeight' => 'required',
        ]);
        //Check existing Culf
        $check_existing_culf = CulfInformation::all();
        foreach ($check_existing_culf as $single_culf){
            if($request->CulfCode == $single_culf->CulfCode)  return response()->json(['message' => 'The Culf Already Exist'], 400);
        }

        if ($validator->fails()){
            return response()->json(['message' => $validator->errors()], 400);
        }
        else{
            try{

                //store culf data
                $storeCulfData = new CulfInformation();
                $storeCulfData->CulfCode = $request->CulfCode;
                $storeCulfData->CulfBirthDate = $request->CulfBirthDate;
                $storeCulfData->Gender = $request->Gender;
                $storeCulfData->Color = $request->Color;
                $storeCulfData->BirthWeight = $request->BirthWeight;
                $storeCulfData->CowCode = $request->CowCode;
                $storeCulfData->BullID = $request->BullID;
                $storeCulfData->EntryBy = Auth::user()->UserID;
                $storeCulfData->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
                $storeCulfData->EntryIPAddress = DeviceService::get_client_ip();
                $storeCulfData->EntryDiviceState = DeviceService::getBrowser();
                $storeCulfData->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
                $storeCulfData->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
                $storeCulfData->save();

                return response()->json([
                    'status' => 'success',
                    'message' =>'Culf Data Added Successfully'
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
    public function getAllCulfData(){
        try{
            $existingData = CulfInformation::select(
                'CulfInformation.CulfID',
                'CulfInformation.CulfCode',
                'CulfInformation.CulfBirthDate',
                'CulfInformation.Gender',
                'CulfInformation.Color',
                'CulfInformation.BirthWeight',
                'CulfInformation.CowCode',
                'CulfInformation.BullID',
                'Bulls.BullName'
            )
                ->join('Bulls','Bulls.BullID', 'CulfInformation.BullID')
                ->where('CulfInformation.EntryBy', Auth::user()->UserID)->get();
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
