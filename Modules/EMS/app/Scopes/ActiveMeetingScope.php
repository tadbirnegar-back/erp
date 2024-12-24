<?php

namespace Modules\EMS\app\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Modules\EMS\app\Http\Enums\MeetingStatusEnum;

class ActiveMeetingScope implements Scope
{

    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereDoesntHave('latestStatus', function ($query) {
            $query->where('name', MeetingStatusEnum::CANCELED->value);
        });
    }
}
