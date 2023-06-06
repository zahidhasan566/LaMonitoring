<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PregnancyInformation extends Model
{
    use HasFactory;
    protected $table = "PregnancyInformation";
    public $primaryKey = 'PregnancyID';
    protected $guarded = [];
    public $timestamps = false;
}
