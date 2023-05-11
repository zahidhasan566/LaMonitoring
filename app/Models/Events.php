<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;
    protected $table = "Events";
    public $primaryKey = 'EventID';
    protected $guarded = [];
    public $timestamps = false;
}
