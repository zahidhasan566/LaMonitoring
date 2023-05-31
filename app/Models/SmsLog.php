<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;
    protected $table = "SmsLog";
    public $primaryKey = 'SmsLogID';
    protected $guarded = [];
    public $timestamps = false;
}
