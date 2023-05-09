<?php

namespace App\Http\Controllers\Admin\Breeding;

use App\Http\Controllers\Controller;
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
}
