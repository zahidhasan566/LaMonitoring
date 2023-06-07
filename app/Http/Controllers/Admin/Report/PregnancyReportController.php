<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Entries;
use App\Models\PregnancyInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PregnancyReportController extends Controller
{
    public function index(Request $request){
        $take = $request->take;
        $search = $request->search;

        $data=  PregnancyInformation::join('Users', 'Users.UserID', 'PregnancyInformation.EntryBy')
            ->join('Cows', 'Cows.CowID', 'PregnancyInformation.CowID')
            ->where(function ($q) use ($search) {
                $q->where('Cows.CowCode', 'like', '%' . $search . '%');
                $q->orWhere('PregnancyInformation.SeedDate', 'like', '%' . $search . '%');
                $q->orWhere('PregnancyInformation.PregnancyTestDate', 'like', '%' . $search . '%');
                $q->orWhere('Users.Name', 'like', '%' . $search . '%');
            })
            ->orderBy('PregnancyInformation.PregnancyID', 'desc')
            ->select('PregnancyInformation.PregnancyID as Pregnancy Id', 'Cows.CowCode','PregnancyInformation.Owner',
                DB::raw("FORMAT(PregnancyInformation.SeedDate,'dd-MM-yyyy') as SeedDate"),
                DB::raw("FORMAT(PregnancyInformation.PregnancyTestDate,'dd-MM-yyyy') as PregnancyTestDate"),
                'PregnancyInformation.PregnancyTestResult as PregnancyTestResult',
                DB::raw("FORMAT(PregnancyInformation.PossibleDeliveryDate,'dd-MM-yyyy') as PossibleDeliveryDate"),
                'Users.Name as Entry By','PregnancyInformation.CreatedAt');

        if(!empty($request->filters[0]['value'])){
            $first = $request->filters[0]['value'][0];
            $second = $request->filters[0]['value'][1];

            $start_date = date("Y-m-d", strtotime($first));
            $end_date = date("Y-m-d", strtotime($second));

            $data =  $data->whereBetween(DB::raw("CONVERT(DATE,PregnancyInformation.CreatedAt)"), [$start_date, $end_date]);
        }
        if(!empty($request->CowCode)){
            $data =  $data->where('Cows.CowCode',$request->CowCode);
        }
        return $data->paginate($take);
    }
}
