<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;
    protected $primaryKey = "attachmentId";
    protected $table = "attachments";

    protected $fillable = [
        'attachmentId',
        'attachmentTitle',
        'attachmentPath',
        'attachmentRelatorSlug',
        'attachmentRelatorId',
    ];
}
