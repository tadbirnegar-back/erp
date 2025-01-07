<?php

namespace Modules\ACC\app\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Modules\ACC\app\Http\Traits\AccountTrait;

class ActiveAccountOnlyScope implements Scope
{
    use AccountTrait;

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply($builder, Model $model): void
    {
        $builder->where('status_id', $this->activeAccountStatus()->id);
    }
}
