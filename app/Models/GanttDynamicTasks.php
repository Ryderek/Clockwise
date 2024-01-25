<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GanttDynamicTasks extends Model
{
    use HasFactory;
    protected $fillable = [
        "text",
        "duration",
        "progress",
        "start_date",
        "parent",
    ];
    protected $appends = ["open"]; 
    public function getOpenAttribute(){
        return true;
    }
}
