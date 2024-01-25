<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTimingHistory extends Model
{
    use HasFactory;
    protected $primaryKey = "workTimingHistoryId";
    protected $table = "work_timings_history";

    protected $fillable = [
        'workTimingHistoryId',
        'workTimingHistoryDetailId',
        'workTimingHistoryDescriptor',
        'workTimingHistoryUserId',
        'workTimingHistoryDetailsDone',
    ];
}
