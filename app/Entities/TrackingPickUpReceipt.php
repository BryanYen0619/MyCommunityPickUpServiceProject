<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class TrackingPickUpReceipt extends Model
{
    protected $fillable = [
        'community_id',
        'user_id',
        'customer_no',
        'pick_up_no',
        'shipper',
        'shipper_phone',
        'shipper_post',
        'shipper_address',
        'consignee',
        'consignee_phone',
        'consignee_post',
        'consignee_address',
        'transport_date',
        'delivery_period',
        'remark'
    ];
}
