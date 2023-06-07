<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Bulls;
use App\Models\CulfInformation;
use App\Models\PregnancyInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CulfReportController extends Controller
{
    public function index(Request $request){
        $take = $request->take;
        $search = $request->search;

        $data=  CulfInformation::join('Users', 'Users.UserID', 'CulfInformation.EntryBy')
            ->join('Bulls', 'Bulls.BullID', 'CulfInformation.BullID')
            ->where(function ($q) use ($search) {
                $q->Where('Bulls.BullName', 'like', '%' . $search . '%');
                $q->orWhere('Users.Name', 'like', '%' . $search . '%');
            })
            ->orderBy('CulfInformation.CulfID', 'desc')
            ->select('CulfInformation.CulfID as CulfId','CulfInformation.CulfCode as CulfName',
                DB::raw("FORMAT(CulfInformation.CulfBirthDate,'dd-MM-yyyy') as CulfBirthDate"),
                'CulfInformation.Gender',
                'CulfInformation.Color','CulfInformation.BirthWeight','CulfInformation.CowCode AS Mother','Bulls.BullName AS Father',
                'Users.Name as Entry By','CulfInformation.CreatedAt');

        if(!empty($request->filters[0]['value'])){
            $first = $request->filters[0]['value'][0];
            $second = $request->filters[0]['value'][1];

            $start_date = date("Y-m-d", strtotime($first));
            $end_date = date("Y-m-d", strtotime($second));

            $data =  $data->whereBetween(DB::raw("CONVERT(DATE,CulfInformation.CreatedAt)"), [$start_date, $end_date]);
        }
        return $data->paginate($take);
    }
}
