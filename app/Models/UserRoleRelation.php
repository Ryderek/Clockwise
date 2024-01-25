<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRoleRelation extends Model
{
    use HasFactory;
    protected $primaryKey = "userRoleRelationId";
    protected $table = "user_role_relations";

    protected $fillable = [
        'userRoleRelationId',
        'userRoleUserId',
        'userRoleRoleId',
    ];
}
