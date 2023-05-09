<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farms extends Model
{
    use HasFactory;
    protected $table = "Farms";
    public $primaryKey = 'FarmID';
    protected $guarded = [];
    public $timestamps = false;
}
