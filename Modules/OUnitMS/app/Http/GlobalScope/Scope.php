<?php

namespace Modules\OUnitMS\App\Http\GlobalScope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class ActiveScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $model = OrganizationUnit::where('status_id', 1)->get();
        $builder->where('status_id', 1);
        return $model;
    }
}
