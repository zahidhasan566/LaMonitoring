<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Farms extends Model
{
    use HasFactory;
    protected $table = "Farms";
    public $primaryKey = 'FarmID';
    protected $guarded = [];
    public $timestamps = false;


    public function Cows() {
        return $this->hasMany(Cows::class,'FarmID','FarmID');
    }
    public function LaInfo() {
        return $this->hasOne(User::class,'UserID','EntryBy');
    }
}
