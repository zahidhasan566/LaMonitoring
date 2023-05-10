<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
