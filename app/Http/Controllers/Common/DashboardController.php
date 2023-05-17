<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Entries;
use App\Models\Events;
use App\Models\Farms;
use App\Models\Menu;
use App\Models\Notices;
use App\Models\ReEntries;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        try {
            $auth = Auth::user();
            $today =  Carbon::now()->format('Y-m-d H:i:s');
            $totalFarms = Farms::all()->count();
            $totalLa = User::where('RoleID','LA')->count();
            $totalBreeding = Entries::all()->count();
            $totalReBreeding = ReEntries::all()->count();

            $events = Events::select('Events.*')->where('Events.EventEndTo','>=',$today)->where('Events.Status',1)->get();

            $notices = Notices::select('Notices.*')->where('Notices.NoticeEndTo','>=',$today)->where('Notices.Status',1)->get();

            return response()->json([
                'status' => 'success',
                'events' => $events,
                'notices' => $notices,
                'totalFarms' => $totalFarms,
                'totalLa' => $totalLa,
                'totalBreeding' => $totalBreeding,
                'totalReBreeding' => $totalReBreeding,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
