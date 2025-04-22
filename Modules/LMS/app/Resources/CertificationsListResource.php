<?php

namespace Modules\LMS\app\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\AAA\app\Models\User;
use Modules\CustomerMS\app\Models\Customer;
use Modules\LMS\app\Models\Course;
use Modules\PersonMS\app\Models\Person;

class CertificationsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $date = convertDateTimeGregorianToJalaliDateTime(Carbon::now());
        $dateOnly = explode(' ', $date)[0];

        $year = Carbon::now()->year;
        $removeSlash = str_replace('/', '', $dateOnly); // "۱۴۰۴۰۲۰۱"
        $final = mb_substr($removeSlash, 2); // removes the first two characters




        return [
            'course_id' => $this->course_id,
            'course_title' => $this->course_title,
            'score' => $this->score,
            'enroll_id' => changeNumbersToPersian($this->enroll_id),
            'student_id' => $this->student_id,
            'user_data' => $this->getUserData($this->student_id),
            'positions' => $this->getPositions($this->student_id),
            'watch_time' => $this->getWatchTime($this->student_id, $this->course_id),
            'year' => $year,
            'date' => $dateOnly,
            'removeSlash' => $final,

        ];
    }

    private function getUserData($student_id)
    {
        $customer = Customer::where('customerable_id', $student_id)->first();
        $person = Person::where('id', $customer->person_id)->first();

        return ['user_name' => $person->display_name, 'national_code' => $person->national_code];
    }

    private function getPositions($student_id)
    {
        $customer = Customer::where('customerable_id', $student_id)->first();
        $person = Person::where('id', $customer->person_id)->first();
        $user = User::where('person_id', $person->id)->first();

        $user->load('activeRecruitmentScripts.position');

        $positions = $user->activeRecruitmentScripts->pluck('position.name')->filter()->unique()->toArray();
        return $positions;

    }

    private function getWatchTime($student_id, $course_id)
    {
        $course = Course::with(['contents' => function ($query) use ($student_id) {
            $query->with(['consumeLogs' => function ($query) use ($student_id) {
                $query->where('student_id', $student_id);
            }]);
            $query->with('file');
        }])->find($course_id);

        $durationsWithConsumeLog = collect($course->contents)
            ->filter(function ($content) {
                return !empty($content->consumeLogs); // <-- fixed here
            })
            ->pluck('file.duration');

        return $durationsWithConsumeLog->sum();

    }
}
