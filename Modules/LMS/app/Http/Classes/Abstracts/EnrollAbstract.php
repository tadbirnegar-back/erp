<?php
namespace Modules\LMS\app\Http\Classes\Abstracts;

use Modules\LMS\app\Models\Enroll;

Abstract class EnrollAbstract
{
    protected int $courseID;
    protected int $enrollID;
    protected function setCourseID(int $courseID): void
    {
        $this->courseID = $courseID;
    }

    protected function getCourseID(): int
    {
        return $this->courseID;
    }

    protected function setEnrollID()
    {
        $courseID = $this->getCourseID();
        $enroll = Enroll::where('course_id',$courseID)->select('id')->firstOrFail();
        if($enroll){
            $this->enrollID = $enroll->id;
        }else{
            $enroll = Enroll::create([
                'course_id' => $courseID,
                'study_completed' => 0,
                'study_count' => 0
            ]);
            $this->enrollID = $enroll->id;
        }
        return $this->enrollID;
    }

    protected function getEnrollID(): int
    {
        return $this->enrollID ?? $this->setEnrollID();
    }

    protected function storeEnroll()
    {
        $this -> getEnrollID();
    }

}
