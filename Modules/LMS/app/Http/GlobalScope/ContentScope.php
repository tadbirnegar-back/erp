<?php

namespace Modules\LMS\app\Http\GlobalScope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Modules\LMS\app\Http\Traits\ContentTrait;
use Modules\OUnitMS\app\Http\Traits\OrganizationUnitTrait;

class ContentScope implements Scope

{
    use ContentTrait;

    public function apply($builder,$model)
    {
        $status = $this->contentActiveStatus()->id;
        $builder->where('status_id', $status);
    }
}
