<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\FileMS\app\Models\File;
use Modules\LMS\app\Http\GlobalScope\ContentScope;
use Modules\LMS\Database\factories\ContentFactory;
use Modules\StatusMS\app\Models\Status;

class Content extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'contents';

    protected $fillable = [
        'id',
        'content_type_id',
        'file_id',
        'lesson_id',
        'name',
        'status_id',
        'teacher_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ContentScope());
    }

    public function contentType()
    {
        return $this->belongsTo(ContentType::class, 'content_type_id', 'id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id', 'id');
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    public function consumeLog()
    {
        return $this->belongsTo(ContentConsumeLog::class, 'id', 'content_id');
    }


    public function consumeLogs()
    {
        return $this->hasMany(ContentConsumeLog::class, 'content_id', 'id');
    }


    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
