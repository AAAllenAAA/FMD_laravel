<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mpnData extends Model
{
    protected $table = "mpn_data";
    protected $primaryKey = 'mpn_id';
    protected $fillable = [
        "manuId",
        "mpn_weight",
        "status_RoHS",
        "exemptions_RoHS",
    ];
}
