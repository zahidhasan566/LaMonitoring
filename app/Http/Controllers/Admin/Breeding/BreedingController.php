<?php

namespace App\Http\Controllers\Admin\Breeding;

use App\Http\Controllers\Controller;
use App\Models\Entries;
use App\Models\Farms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BreedingController extends Controller
{
    //List View DataTable
    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;

        $entries=  Entries::join('Users', 'Users.UserID', 'Entries.EntryBy')
            ->join('Cows', 'Cows.CowID', 'Entries.CowID')
            ->join('Farms', 'Farms.FarmID', 'Entries.FarmID')
            ->where(function ($q) use ($search) {
                $q->where('Cows.CowCode', 'like', '%' . $search . '%');
                $q->orWhere('Farms.FarmName', 'like', '%' . $search . '%');
                $q->orWhere('Entries.HotDate', 'like', '%' . $search . '%');
                $q->orWhere('Entries.SeedDate', 'like', '%' . $search . '%');
                $q->orWhere('Entries.TestDate', 'like', '%' . $search . '%');
                $q->orWhere('Entries.BirthDate', 'like', '%' . $search . '%');
                $q->orWhere('Users.Name', 'like', '%' . $search . '%');
            })
            ->orderBy('Farms.FarmName', 'asc')
            ->select('Entries.EntryID', 'Cows.CowCode','Farms.FarmName','Entries.Age','Entries.MilkCap as Milk Capacity',
                 DB::raw("FORMAT(Entries.HotDate,'dd-MM-yyyy') as HotDate"),
                 DB::raw("FORMAT(Entries.SeedDate,'dd-MM-yyyy') as SeedDate"),
                 DB::raw("FORMAT(Entries.TestDate,'dd-MM-yyyy') as TestDate"),
                 DB::raw("FORMAT(Entries.BirthDate,'dd-MM-yyyy') as BirthDate"),
                 'Users.Name as Entry By','Entries.CreatedAt');

        if(!empty($request->filters[0]['value'])){
            $first = $request->filters[0]['value'][0];
            $second = $request->filters[0]['value'][1];

            $start_date = date("Y-m-d", strtotime($first));
            $end_date = date("Y-m-d", strtotime($second));

            $entries =  $entries->whereBetween(DB::raw("
            ."), [$start_date, $end_date]);
        }
        return $entries->paginate($take);
    }
}
