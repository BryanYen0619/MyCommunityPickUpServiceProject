<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class PackageInfo extends Model
{
    protected $fillable = [
        'tracking_pick_up_id',
        'tracking_number',
        'piece',
        'carton_size',
        'receive_date',
        'receive_time',
        'pick_up_no',
        'status',
        'station',
        'message'
    ];
}
