<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notices extends Model
{
    use HasFactory;
    protected $table = "Notices";
    public $primaryKey = 'NoticeID';
    protected $guarded = [];
    public $timestamps = false;
}
