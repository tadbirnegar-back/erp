<?php

namespace Modules\LMS\app\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\LMS\app\Http\Traits\CourseTrait;
use Modules\LMS\app\Models\Course;

class RelatedCourseListResource extends JsonResource
{
    use CourseTrait;

    public function toArray($request): array
    {

    }



}
