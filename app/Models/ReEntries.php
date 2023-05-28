<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReEntries extends Model
{
    use HasFactory;
    protected $table = "ReEntries";
    public $primaryKey = 'ReID';
    protected $guarded = [];
    public $timestamps = false;
    public function LaInfo() {
        return $this->hasOne(User::class,'UserID','EntryBy');
    }

    public function farms() {
        return $this->belongsTo(Farms::class,'FarmID','FarmID');
    }

    public function cows() {
        return $this->belongsTo(Cows::class,'CowID','CowID');
    }

}
