<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AAA\app\Models\Module;
use Modules\AAA\app\Models\ModuleCategory;
use Modules\AAA\app\Models\Permission;

class EMSPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apiRoutes = collect([
            [
                'name' => 'افزودن مصوبه توسط هیئت',
                'slug' => '/mes/enactment/add-by-board'
            ],
            [
                'name' => 'جستجوی روستاهای واحد سازمانی',
                'slug' => '/mes/ounit-villages/search'
            ],
            [
                'name' => 'لیست مصوبات در انتظار دبیر',
                'slug' => '/mes/pbs-enactments/list'
            ],
            [
                'name' => 'لیست مصوبات در انتظار هیئت',
                'slug' => '/mes/pbc-enactments/list'
            ],
            [
                'name' => 'لیست تمامی مصوبات',
                'slug' => '/mes/all-enactments/list'
            ],
            [
                'name' => 'جزئیات مصوبه',
                'slug' => '/mes/enactments/{id}'
            ],
            [
                'name' => 'تأیید مصوبه',
                'slug' => '/mes/enactments/approve/{id}'
            ],
            [
                'name' => 'رد مصوبه',
                'slug' => '/mes/enactments/decline/{id}'
            ],
            [
                'name' => 'رد مصوبه',
                'slug' => '/mes/enactments/deny/{id}'
            ],
            [
                'name' => 'پذیرش مصوبه',
                'slug' => '/mes/enactments/accept/{id}'
            ],
            [
                'name' => 'تنظیمات دبیر',
                'slug' => '/mes/setting/secretary'
            ],
            [
                'name' => 'تنظیمات دبیر',
                'slug' => '/mes/setting/secretary'
            ]
        ]);

        \DB::transaction(function () use ($apiRoutes) {
            $moduldeCat = ModuleCategory::create([
                'name' => 'مدیریت مصوبات',
                'icon' => 'EnactmentIcon',

            ]);
            $module = Module::create([
                'name' => 'EMS',
                'module_category_id' => $moduldeCat->id,

            ]);

            $dataToInsert = $apiRoutes->map(function ($route) use ($module) {
                if (str_contains($route['slug'], 'add') || str_contains($route['slug'], 'list') || str_contains($route['slug'], 'setting')) {
                    $ptID = 1;
                } else {
                    $ptID = 2;
                }
                return [
                    'name' => $route['name'],
                    'slug' => $route['slug'],
                    'module_id' => $module->id,
                    'permission_type_id' => $ptID
                ];
            })->toArray();

            Permission::upsert($dataToInsert, ['slug']);

        });


    }
}
