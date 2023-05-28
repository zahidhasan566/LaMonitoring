<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Entries extends Model
{
    use HasFactory;
    protected $table = "Entries";
    public $primaryKey = 'EntryID';
    protected $guarded = [];
    public $timestamps = false;

    public function LaInfo() {
        return $this->hasOne(User::class,'UserID','EntryBy');
    }
//    public function rebreedingCount()
//    {
//        return $allReBreedingInfo = ReEntries::join('Farms','Farms.FarmID','ReEntries.FarmID')
//            ->join('Cows','Cows.CowID','ReEntries.CowID')
//            ->select(DB::raw("COUNT(EntryID) as ReBreedCount"))
//            ->groupBy('EntryID')
//            ->get();
////        $totalRebredding
////        return $this->hasMany(ReEntries::class,'EntryID');
//    }
}
