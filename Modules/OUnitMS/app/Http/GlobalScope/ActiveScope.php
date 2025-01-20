<?php

namespace Modules\OUnitMS\app\Http\GlobalScope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Log;
use Modules\OUnitMS\app\Http\Traits\OrganizationUnitTrait;

class ActiveScope implements Scope

{
    use OrganizationUnitTrait;

    public function apply(Builder $builder, Model $model)
    {
//        $model = OrganizationUnit::where('status_id', 1)->get();
        $status = $this->getActiveStatuses();
        $builder->where('status_id', $status->id);
//        return $model;
    }
}
