<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory; 
    protected $primaryKey = "orderDetailId";
    protected $table = "order_details";

    protected $fillable = [
        'orderDetailId',
        'orderDetailUniqueId',
        'orderDetailOrderId',
        'orderDetailOrderNumber',
        'orderDetailName',
        'orderDetailUnitProductionCost',
        'orderDetailUnitSellValue',
        'orderDetailItemsDone',
        'orderDetailItemsTotal',
        'orderDetailPainting',
        'orderDetailCooperation',
        'orderDetailDescriptor',
        'orderDetailIsDeleted',
    ];
}
