<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $primaryKey = "orderId";
    protected $table = "orders";

    protected $fillable = [
        'orderName',
        'orderStatus',
        'orderCustomer',
        'orderDeadline',
        'orderStatusLight',
        'orderValue',
        'orderCooperated',
        'orderAdditionalField',
        'orderCreatedBy',
        'orderCreatedTime',
        'orderConfirmedBy',
        'orderConfirmedTime',
        'orderPublishedBy',
        'orderPublishedTime',
        'orderDoneBy',
        'orderDoneTime',
        'orderIsDeleted',
    ];
}
