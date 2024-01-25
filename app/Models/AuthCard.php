<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthCard extends Model
{
    use HasFactory;

    protected $primaryKey = "authCardId";
    protected $table = "auth_cards";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'authCardUserId',
        'authCardCode',
    ];
}
