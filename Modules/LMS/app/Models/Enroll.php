<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\FileMS\app\Models\File;
use Modules\LMS\Database\factories\EnrollFactory;
use Modules\PayStream\app\Models\Order;

class Enroll extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'enrolls';

    protected $fillable = [
        'id',
        'course_id',
        'certificate_file_id',
        'study_completed',
        'study_count'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function certificateFile()
    {
        return $this->belongsTo(File::class, 'certificate_file_id', 'id');
    }

    public function order()
    {
        return $this->morphOne(Order::class, 'orderable');
    }

}
