<?php

namespace App\Http\Controllers\Mobile\La\Dashborad;

use App\Http\Controllers\Controller;
use App\Models\Entries;
use App\Models\Events;
use App\Models\Farms;
use App\Models\Notices;
use App\Models\ReEntries;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use stdClass;

class DashboardController extends Controller
{
    public function index(){
        try {
            $auth = Auth::user();
            $today =  Carbon::now()->format('Y-m-d H:i:s');
            $totalFarms = Farms::all()->count();
            $totalLa = User::where('RoleID','LA')->count();
            $totalBreeding = Entries::where('EntryBy',Auth::user()->UserID)->count();
            $totalReBreeding = ReEntries::all()->count();

            $events = Events::select('Events.*')->where('Events.EventEndTo','>=',$today)->where('Events.Status',1)->get();

            $notices = Notices::select('Notices.*')->where('Notices.NoticeEndTo','>=',$today)->where('Notices.Status',1)->get();

            $eventItems = [];
            foreach ($events as $key=>$single_event){
                $imageItem=[];
                $imageItem['image']= url("public/uploads/").'/'.$single_event->EventImage ;
                array_push($eventItems, $imageItem);
            }
            return response()->json([
                'status' => 'success',
                'events' => $eventItems,
                'notices' => $notices,
                'totalBreeding' => $totalBreeding,

            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
