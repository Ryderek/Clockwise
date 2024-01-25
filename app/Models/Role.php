<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory; 
    protected $primaryKey = "roleId";
    protected $table = "roles";

    protected $fillable = [
        'roleName',
        'roleProcess',
        'roleSlug',
        'roleStations',
    ];
}
