<?php

namespace Modules\LMS\app\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use League\CommonMark\Node\NodeWalker;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\StatusCourse;

class CourseExpirationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CourseTrait;

    public $courseId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $course)
    {
        \Log::info($course);
        $this->courseId = $course;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $course = Course::find($this->courseId);
        $date = convertPersianToGregorianBothHaveTimeAndDont($course->expiration_date);
        $date = Carbon::parse($date);
        if ( $date != null && $date->format('Y-m-d') == now()->format('Y-m-d')) {
            StatusCourse::create([
                'course_id' => $this->courseId,
                'status_id' => $this->courseEndedStatus()->id,
                'create_date' => now(),
                'description' => null
            ]);
        }else {
            \Log::info('not');
        }
    }
}
