<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cows extends Model
{
    use HasFactory;
    protected $table = "Cows";
    public $primaryKey = 'CowID';
    protected $guarded = [];
    public $timestamps = false;
}
