<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bulls extends Model
{
    use HasFactory;
    protected $table = "Bulls";
    public $primaryKey = 'BullID';
    protected $guarded = [];
    public $timestamps = false;

}
