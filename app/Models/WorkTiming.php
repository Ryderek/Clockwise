<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTiming extends Model
{
    use HasFactory;
    protected $primaryKey = "workTimingId";
    protected $table = "work_timings";

    protected $fillable = [
        'workTimingId',
        'workTimingUserId',
        'workTimingRelatorId',
        'workTimingRelatorParentId',
        'workTimingRoleSlug',
        'workTimingType',
        'workTimingStart',
        'workTimingEnd',
        'workTimingFinal',
    ];
}
