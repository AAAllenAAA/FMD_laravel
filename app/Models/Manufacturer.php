<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    protected $table = "manufacturer";
    protected $primaryKey = 'manu_id';
    protected $fillable = [
        "manu_name",
        "manu_PartNumber",
        "manu_model",
    ];

}
