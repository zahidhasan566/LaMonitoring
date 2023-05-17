<?php

namespace App\Http\Controllers\Admin\Breeding;

use App\Http\Controllers\Controller;
use App\Models\Bulls;
use App\Models\BullTypes;
use App\Models\Menu;
use App\Services\DeviceService;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BullController extends Controller
{
    //List View DataTable
    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;

        $farms=  Bulls::join('Users', 'Users.UserID', 'bulls.EntryBy')
            ->join('bullTypes', 'bullTypes.BullTypeID', 'bulls.BullTypeID')
            ->where(function ($q) use ($search) {
                $q->where('bulls.BullName', 'like', '%' . $search . '%');
            })
            ->orderBy('bulls.BullName', 'asc')
            ->select('bulls.BullID','bulls.BullName', 'bulls.BullCode','bullTypes.BullTypeName','Users.Name as Entry By','bulls.CreatedAt');

        if(!empty($request->filters[0]['value'])){
            $first = $request->filters[0]['value'][0];
            $second = $request->filters[0]['value'][1];

            $start_date = date("Y-m-d", strtotime($first));
            $end_date = date("Y-m-d", strtotime($second));

            $farms =  $farms->whereBetween(DB::raw("CONVERT(DATE,bulls.CreatedAt)"), [$start_date, $end_date]);
        }
        return $farms->paginate($take);
    }

    public function bullModalData(){
        return response()->json([
            'status' => 'success',
            'BullType' => BullTypes::all(),
        ]);
    }

    //Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'BullTypeID' => 'required',
            'BullName' => 'required|string',
            'BullCode' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        //Data Insert
        try {
            DB::beginTransaction();
            $bulls = new Bulls();
            $bulls->BullName = $request->BullName;
            $bulls->BullTypeID = $request->BullTypeID;
            $bulls->BullCode = $request->BullCode;

            $bulls->EntryBy = Auth::user()->UserID;
            $bulls->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
            $bulls->EntryIPAddress = DeviceService::get_client_ip();
            $bulls->EntryDiviceState = DeviceService::getBrowser();
            $bulls->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $bulls->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $bulls->save();

            DB::commit();


            return response()->json([
                'status' => 'success',
                'message' => 'Bull Created Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }

    //Get Existing Bull Type Info
    public function getBullInfo($BullID){
        $bull = Bulls::where('BullID', $BullID)->first();

        return response()->json([
            'status' => 'success',
            'data' => $bull
        ]);
    }

    //Update Bull Data
    public function updateBullData(Request $request){

        $validator = Validator::make($request->all(), [
            'BullTypeID' => 'required',
            'BullName' => 'required|string',
            'BullCode' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        try {
            DB::beginTransaction();
            $bulls = Bulls::where('BullID', $request->BullID)->first();
            $bulls->BullName = $request->BullName;
            $bulls->BullTypeID = $request->BullTypeID;
            $bulls->BullCode = $request->BullCode;

            $bulls->EntryBy = Auth::user()->UserID;
            $bulls->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
            $bulls->EntryIPAddress = DeviceService::get_client_ip();
            $bulls->EntryDiviceState = DeviceService::getBrowser();
            $bulls->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $bulls->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $bulls->save();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Bull  Updated Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
