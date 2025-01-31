<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\ContentConsumeLogFactory;

class ContentConsumeLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    public $table = 'content_consume_log';

    protected $fillable = [
        'id',
        'content_id',
        'consume_data',
        'consume_round',
        'create_date',
        'last_modified',
        'student_id',
        'last_played',
        'set'
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
