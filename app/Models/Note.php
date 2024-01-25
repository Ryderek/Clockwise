<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory; 
    protected $primaryKey = "noteId";
    protected $table = "notes";

    protected $fillable = [
        'noteId',
        'noteTitle',
        'noteContent',
        'noteRelatorId',
        'noteRelatorSlug',
    ];
}
