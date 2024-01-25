<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasFactory;    
    protected $primaryKey = "toolId";
    protected $table = "tools";

    protected $fillable = [
        'toolId',
        'toolName',
        'toolStatus',
        'toolLastRepaired',
        'toolUpdatedBy',
    ];
}
