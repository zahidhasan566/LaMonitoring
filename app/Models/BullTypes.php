<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BullTypes extends Model
{
    use HasFactory;
    protected $table = "bullTypes";
    public $primaryKey = 'BullTypeID';
    protected $guarded = [];
    public $timestamps = false;
}
