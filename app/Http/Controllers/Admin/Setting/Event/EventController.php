<?php

namespace App\Http\Controllers\Admin\Setting\Event;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Farms;
use App\Services\DeviceService;
use App\Services\ImageBase64Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $request){
        $take = $request->take;
        $search = $request->search;


        $events =  Events::join('Users', 'Users.UserID', 'Events.EntryBy')
            ->where(function ($q) use ($search) {
                $q->where('Events.EventID', 'like', '%' . $search . '%');
            })
            ->orderBy('Events.EventID', 'asc')
            ->select('Events.EventID', 'Events.EventImage', 'Events.EventStartFrom','Events.EventEndTo','Events.Status','Users.Name as Entry By','Events.CreatedAt');

        return $events->paginate($take);

    }
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'attachment' => 'required|string',
            'EventStartFrom' => 'required|date',
            'EventEndTo' => 'required|date',
            'Status' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        //Data Insert
        try {
            DB::beginTransaction();
            $event = new Events();
            $event->EventImage = ImageBase64Service::imageUpload($request->attachment,'EventImage',public_path('uploads/'));
            $event->EventStartFrom = $request->EventStartFrom;
            $event->EventEndTo = $request->EventEndTo;
            $event->Status = $request->Status;

            $event->EntryBy = Auth::user()->UserID;
            $event->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
            $event->EntryIPAddress = DeviceService::get_client_ip();
            $event->EntryDiviceState = DeviceService::getBrowser();
            $event->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $event->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $event->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Event Created Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }

    }
}
