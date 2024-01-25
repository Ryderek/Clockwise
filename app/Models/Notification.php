<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $primaryKey = "notificationId";
    protected $table = "notifications";

    protected $fillable = [
        'notificationId',
        'notificationSenderId',
        'notificationContent',
        'notificationIsDismissed'
    ];
}
