<?php

namespace Modules\ACC\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACC\Database\factories\DocumentStatusFactory;

class DocumentStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'document_id',
        'status_id',
        'creator_id',
    ];

    public $timestamps = false;
    protected $table = 'accDocument_status';

}
