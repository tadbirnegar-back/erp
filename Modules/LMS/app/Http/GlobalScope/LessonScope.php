<?php

namespace Modules\LMS\app\Http\GlobalScope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Modules\LMS\app\Http\Traits\LessonTrait;

class LessonScope implements Scope
{
    use LessonTrait;

    public function apply(Builder $builder, Model $model)
    {
        $status = $this->lessonActiveStatus()->id; // Ensure this method is accessible

        $builder->whereHas('latestStatus', function (Builder $query) use ($status) {
            $query->where('statuses.id', $status);
        });
    }
}
