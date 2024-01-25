<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = "customers";
    protected $primaryKey = "customerId";

    protected $fillable = [
        'customerName',
        'customerTaxIdentityNumber',
        'customerCountry',
        'customerCity',
        'customerPostal',
        'customerAddress',
        'customerDeliveryCountry',
        'customerDeliveryCity',
        'customerDeliveryPostal',
        'customerDeliveryAddress',
    ];
}
