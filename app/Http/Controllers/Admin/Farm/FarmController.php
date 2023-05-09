<?php

namespace App\Http\Controllers\Admin\Farm;

use App\Http\Controllers\Controller;
use App\Models\Farms;
use App\Models\SubMenuPermission;
use App\Models\User;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FarmController extends Controller
{

    //List View DataTable
    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;

        $farms=  Farms::join('Users', 'Users.UserID', 'Farms.EntryBy')
            ->where(function ($q) use ($search) {
            $q->where('Farms.FarmName', 'like', '%' . $search . '%');
            $q->where('Farms.Owner', 'like', '%' . $search . '%');
            $q->where('Farms.RegistrationNumber', 'like', '%' . $search . '%');
            $q->orWhere('Farms.Mobile', 'like', '%' . $search . '%');
            $q->orWhere('Farms.Address', 'like', '%' . $search . '%');
        })
            ->orderBy('Farms.FarmName', 'asc')
            ->select('Farms.FarmID', 'Farms.FarmName', 'Farms.Owner','Farms.RegistrationNumber','Farms.Mobile', 'Farms.Address','Users.Name as Entry By','Farms.CreatedAt');

        if(!empty($request->filters[0]['value'])){
            $first = $request->filters[0]['value'][0];
            $second = $request->filters[0]['value'][1];

            $start_date = date("Y-m-d", strtotime($first));
            $end_date = date("Y-m-d", strtotime($second));

            $farms =  $farms->whereBetween(DB::raw("CONVERT(DATE,Farms.CreatedAt)"), [$start_date, $end_date]);
        }
        return $farms->paginate($take);
    }

    //Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'FarmName' => 'required|string',
            'Owner' => 'required',
            'Address' => 'required',
            'registrationNumber' => 'required',
            'mobile' => 'required'
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
            $farm->RegistrationNumber = $request->registrationNumber;
            $farm->Mobile = $request->mobile;
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
            ], 500);
        }
    }

    //Get Existing farm Info
    public function getFarmInfo($FarmID){
        $farm = farms::where('FarmID', $FarmID)->first();

        return response()->json([
            'status' => 'success',
            'data' => $farm
        ]);
    }

    //Update Farm Data
    public function updateFarmData(Request $request){

        $validator = Validator::make($request->all(), [
            'FarmName' => 'required|string',
            'Owner' => 'required',
            'Address' => 'required',
            'registrationNumber' => 'required',
            'mobile' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        try {
            DB::beginTransaction();
            $farm = Farms::where('FarmID', $request->FarmID)->first();
            $farm->FarmName = $request->FarmName;
            $farm->Owner = $request->Owner;
            $farm->RegistrationNumber = $request->registrationNumber;
            $farm->Mobile = $request->mobile;
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
                'message' => 'Farm Updated Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
