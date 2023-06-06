<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CulfInformation extends Model
{
    use HasFactory;
    protected $table = "CulfInformation";
    public $primaryKey = 'CulfID';
    protected $guarded = [];
    public $timestamps = false;

}
