<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\StatusCourseFactory;
use Modules\StatusMS\app\Models\Status;

class StatusCourse extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    protected $table = 'status_course';

    protected $fillable = [
        "id",
        "course_id",
        "status_id",
        "create_date",
        "description"
    ];

    public $timestamps = false;

    public function status()
    {
        return $this->belongsTo(Status::class);
    }


}
