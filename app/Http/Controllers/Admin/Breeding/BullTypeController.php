<?php

namespace App\Http\Controllers\Admin\Breeding;

use App\Http\Controllers\Controller;
use App\Models\BullTypes;
use App\Models\Farms;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BullTypeController extends Controller
{
    //List View DataTable
    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;

        $farms=  BullTypes::join('Users', 'Users.UserID', 'bullTypes.EntryBy')
            ->where(function ($q) use ($search) {
                $q->where('bullTypes.BullTypeName', 'like', '%' . $search . '%');
            })
            ->orderBy('bullTypes.BullTypeName', 'asc')
            ->select('bullTypes.BullTypeID', 'bullTypes.BullTypeName','Users.Name as Entry By','bullTypes.CreatedAt');

        if(!empty($request->filters[0]['value'])){
            $first = $request->filters[0]['value'][0];
            $second = $request->filters[0]['value'][1];

            $start_date = date("Y-m-d", strtotime($first));
            $end_date = date("Y-m-d", strtotime($second));

            $farms =  $farms->whereBetween(DB::raw("CONVERT(DATE,bullTypes.CreatedAt)"), [$start_date, $end_date]);
        }
        return $farms->paginate($take);
    }

    //Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'BullTypeName' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        //Data Insert

        try {
            DB::beginTransaction();
            $bullTypes = new BullTypes();
            $bullTypes->BullTypeName = $request->BullTypeName;

            $bullTypes->EntryBy = Auth::user()->UserID;
            $bullTypes->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
            $bullTypes->EntryIPAddress = DeviceService::get_client_ip();
            $bullTypes->EntryDiviceState = DeviceService::getBrowser();
            $bullTypes->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $bullTypes->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $bullTypes->save();

            DB::commit();


            return response()->json([
                'status' => 'success',
                'message' => 'Bull Type Created Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }

    //Get Existing Bull Type Info
    public function getBullTypeInfo($BullTypeID){
        $bullType = BullTypes::where('BullTypeID', $BullTypeID)->first();

        return response()->json([
            'status' => 'success',
            'data' => $bullType
        ]);
    }

    //Update Farm Data
    public function updateBullTypeData(Request $request){

        $validator = Validator::make($request->all(), [
            'BullTypeName' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        try {
            DB::beginTransaction();
            $bullTypes = BullTypes::where('BullTypeID', $request->BullTypeID)->first();
            $bullTypes->BullTypeName = $request->BullTypeName;

            $bullTypes->EntryBy = Auth::user()->UserID;
            $bullTypes->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
            $bullTypes->EntryIPAddress = DeviceService::get_client_ip();
            $bullTypes->EntryDiviceState = DeviceService::getBrowser();
            $bullTypes->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $bullTypes->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $bullTypes->save();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Bull Type Updated Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
