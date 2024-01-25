<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailDeployed extends Model
{
    use HasFactory;

    protected $table = "details_deployed";
    protected $primaryKey = "deployedDetailId";

    protected $fillable = [
        "deployedDetailId",
        "deployedDetailOrderId",
        "deployedDetailDetailId",
        "deployedDetailOrderNumber",
        "deployedDetailEAN",
        "deployedDetailIsDeployed",
    ];
}
