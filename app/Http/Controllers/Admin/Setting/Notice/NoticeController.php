<?php

namespace App\Http\Controllers\Admin\Setting\Notice;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Notices;
use App\Services\DeviceService;
use App\Services\ImageBase64Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;


        $notices = Notices::join('Users', 'Users.UserID', 'Notices.EntryBy')
            ->where(function ($q) use ($search) {
                $q->where('Notices.NoticeID', 'like', '%' . $search . '%');
                $q->orWhere('Notices.NoticeTitle', 'like', '%' . $search . '%');
            })
            ->orderBy('Notices.NoticeID', 'asc')
            ->select('Notices.NoticeID', 'Notices.NoticeTitle', 'Notices.NoticeDescription', 'Notices.NoticeStartFrom', 'Notices.NoticeEndTo', 'Notices.Status', 'Users.Name as Entry By', 'Notices.CreatedAt');

        if (!empty($request->filters[0]['value'])) {
            $first = $request->filters[0]['value'][0];
            $second = $request->filters[0]['value'][1];

            $start_date = date("Y-m-d", strtotime($first));
            $end_date = date("Y-m-d", strtotime($second));

            $notices = $notices->whereBetween(DB::raw("CONVERT(DATE,Notices.CreatedAt)"), [$start_date, $end_date]);
        }

        return $notices->paginate($take);

    }

    //Store Notice Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'NoticeTitle' => 'required|string',
            'NoticeStartFrom' => 'required|date',
            'NoticeEndTo' => 'required|date',
            'NoticeDescription' => 'required|string',
            'Status' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        //Data Insert
        try {
            DB::beginTransaction();
            $notice = new Notices();
            $notice->NoticeTitle = $request->NoticeTitle;
            $notice->NoticeStartFrom = $request->NoticeStartFrom;
            $notice->NoticeEndTo = $request->NoticeEndTo;
            $notice->NoticeDescription = $request->NoticeDescription;
            $notice->Status = $request->Status;

            $notice->EntryBy = Auth::user()->UserID;
            $notice->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
            $notice->EntryIPAddress = DeviceService::get_client_ip();
            $notice->EntryDiviceState = DeviceService::getBrowser();
            $notice->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $notice->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $notice->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Notice Created Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }

    //Check Existing Notice

    public function getNoticeInfo($NoticeID){
        $notice = Notices::where('NoticeID', $NoticeID)->first();

        return response()->json([
            'status' => 'success',
            'data' => $notice
        ]);
    }

    //Update Notice Data
    public function updateNoticeData(Request $request){
        $validator = Validator::make($request->all(), [
            'NoticeTitle' => 'required|string',
            'NoticeStartFrom' => 'required|date',
            'NoticeEndTo' => 'required|date',
            'NoticeDescription' => 'required|string',
            'Status' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        //Data Insert
        try {
            DB::beginTransaction();
            $notice = Notices::where('NoticeID', $request->NoticeID)->first();
            $notice->NoticeTitle = $request->NoticeTitle;
            $notice->NoticeStartFrom = $request->NoticeStartFrom;
            $notice->NoticeEndTo = $request->NoticeEndTo;
            $notice->NoticeDescription = $request->NoticeDescription;
            $notice->Status = $request->Status;

            $notice->EntryBy = Auth::user()->UserID;
            $notice->EntryDate = Carbon::now()->format('Y-m-d H:i:s');
            $notice->EntryIPAddress = DeviceService::get_client_ip();
            $notice->EntryDiviceState = DeviceService::getBrowser();
            $notice->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $notice->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $notice->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Notice Updated Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }

    }



}
