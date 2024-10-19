<?php

namespace Modules\EMS\app\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Modules\EMS\app\Http\Enums\EnactmentTitleStatusEnum;

class ActiveStatusScope implements Scope
{

    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereHas('status', function ($query) {
            $query->where('name', EnactmentTitleStatusEnum::ACTIVE->value);
        });
    }
}
