<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AAA\app\Models\Permission;

class OunitMSPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apiRoutes = collect([
            [
                'name' => 'لیست شهرستان ها',
                'slug' => '/oms/cityofc/list'
            ],
            [
                'name' => 'افزودن شهرستان',
                'slug' => '/oms/cityofc/add'
            ],
            [
                'name' => 'لیست بخشداری',
                'slug' => '/oms/districtofc/list'
            ],
            [
                'name' => 'افزودن بخشداری',
                'slug' => '/oms/districtofc/add'
            ],
            [
                'name' => 'لیست دهستان',
                'slug' => '/oms/townofc/list'
            ],
            [
                'name' => 'افزودن دهستان',
                'slug' => '/oms/townofc/add'
            ],
            [
                'name' => 'لیست روستاها',
                'slug' => '/oms/villageofc/list'
            ],
            [
                'name' => 'افزودن روستا',
                'slug' => '/oms/villageofc/add'
            ],
            [
                'name' => 'جزئیات واحد سازمانی',
                'slug' => '/oms/organization_unit/{id}'
            ],
            [
                'name' => 'بروزرسانی واحد سازمانی',
                'slug' => '/oms/organization_unit/update/{id}'
            ],
            [
                'name' => 'جستجوی کارمند',
                'slug' => '/oms/employee/search'
            ],
            [
                'name' => 'جستجوی واحد سازمانی',
                'slug' => '/oms/organization-unit/search'
            ]
        ]);

        $dataToInsert = $apiRoutes->map(function ($route) {
            if (str_contains($route['slug'], 'add') || str_contains($route['slug'], 'list')) {
                $ptID = 1;
            } else {
                $ptID = 2;
            }
            return [
                'name' => $route['name'],
                'slug' => $route['slug'],
                'module_id' => 8,
                'permission_type_id' => $ptID
            ];
        })->toArray();

        \DB::transaction(function () use ($dataToInsert) {
            Permission::upsert($dataToInsert, ['slug']);

        });
    }
}
