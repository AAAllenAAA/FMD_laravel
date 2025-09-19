<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homogeneous extends Model
{
    public $timestamps = false;
    protected $table = "homogeneous_data";
    protected $primaryKey = 'homo_id';
    protected $fillable = [
        'homo_MaterialName',
        'homo_MaterialWeight',
        'subitem_name',
        'manuId',
    ];
}
