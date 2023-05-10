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
}
