<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubstanceData extends Model
{   
    protected $table = "substance_data";
    protected $primaryKey = 'sub_id';

    protected $fillable = [
        'sub_name',
        'sub_CAS',
        'sub_Weight',
        'ppm',
        'sub_exemption',
        'manuId',
    ];
}
