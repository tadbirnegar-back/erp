<?php

namespace Modules\OUnitMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

class OrganizationParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villages = OrganizationUnit::where('unitable_type','!=',StateOfc::class)->with('unitable')->get();

        \DB::transaction(function () use ($villages) {
            $villages->each(function (OrganizationUnit $organizationUnit, int $key) {
//                dd($organizationUnit);

                $parent = $organizationUnit->unitable?->parent->organizationUnit;
                $organizationUnit->parent_id = $parent->id;
                $organizationUnit->save();
            });
        });
        // $this->call([]);
    }
}
