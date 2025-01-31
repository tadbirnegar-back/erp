<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\FileMS\app\Models\File;
use Modules\LMS\Database\factories\FileLessonFactory;

class FileLesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'file_lesson';

    protected $fillable = [
        'id',
        'file_id',
        'lesson_id',
        'title'
    ];


    public function file()
    {
        return $this->belongsTo(File::class , 'file_id' , 'id');
    }

}
