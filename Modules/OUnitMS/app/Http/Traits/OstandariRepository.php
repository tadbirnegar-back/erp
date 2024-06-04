<?php

namespace Modules\OUnitMS\app\Http\Traits;

use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;

trait OstandariRepository
{
    public function ostandariIndex($searchTerm='',int $perPage=10,int $pageNum=1)
    {
        $ostans = OrganizationUnit::where('unitable_type', StateOfc::class)->when($searchTerm, function ($query) use ($searchTerm) {
            $query->whereRaw("MATCH (name) AGAINST (? IN BOOLEAN MODE)", [$searchTerm]);
        })->paginate($perPage,page: $pageNum);


        return $ostans;

    }
}
