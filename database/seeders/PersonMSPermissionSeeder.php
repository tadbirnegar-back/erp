<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AAA\app\Models\Permission;

class PersonMSPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apiRoutes = collect([
            [
                'name' => 'جستجوی شخص حقیقی',
                'slug' => '/person/natural/search'
            ],
            [
                'name' => 'لیست ادیان افراد',
                'slug' => '/person/religions/list'
            ],
            [
                'name' => 'لیست وضعیت نظام وظیفه',
                'slug' => '/person/military-status/list'
            ],
            [
                'name' => 'لاگ افراد',
                'slug' => '/person/log/{id}'
            ],
            [
                'name' => 'بروزرسانی اطلاعات کاربری',
                'slug' => '/person/user-data/update/{id}'
            ],
            [
                'name' => 'بروزرسانی اطلاعات شخصی',
                'slug' => '/person/personal-data/update/{id}'
            ],
            [
                'name' => 'بروزرسانی کد پرسنلی',
                'slug' => '/person/personnel-code/update/{id}'
            ],
            [
                'name' => 'افزودن مهارت شخصی',
                'slug' => '/person/skills/add/{id}'
            ],
            [
                'name' => 'ویرایش مهارت شخصی',
                'slug' => '/person/skills/edit/{id}'
            ],
            [
                'name' => 'حذف مهارت شخصی',
                'slug' => '/person/skills/delete/{id}'
            ],
            [
                'name' => 'افزودن تحصیلات شخصی',
                'slug' => '/person/educations/add/{id}'
            ],
            [
                'name' => 'ویرایش تحصیلات شخصی',
                'slug' => '/person/educations/edit/{id}'
            ],
            [
                'name' => 'حذف تحصیلات شخصی',
                'slug' => '/person/educations/delete/{id}'
            ],
            [
                'name' => 'افزودن سابقه دوره',
                'slug' => '/person/course-record/add/{id}'
            ],
            [
                'name' => 'ویرایش سابقه دوره',
                'slug' => '/person/course-record/edit/{id}'
            ],
            [
                'name' => 'حذف سابقه دوره',
                'slug' => '/person/course-record/delete/{id}'
            ],
            [
                'name' => 'افزودن رزومه',
                'slug' => '/person/resume/add/{id}'
            ],
            [
                'name' => 'ویرایش رزومه',
                'slug' => '/person/resume/edit/{id}'
            ],
            [
                'name' => 'حذف رزومه',
                'slug' => '/person/resume/delete/{id}'
            ],
            [
                'name' => 'افزودن وابستگان',
                'slug' => '/person/relative/add/{id}'
            ],
            [
                'name' => 'ویرایش وابستگان',
                'slug' => '/person/relative/edit/{id}'
            ],
            [
                'name' => 'حذف وابستگان',
                'slug' => '/person/relative/delete/{id}'
            ],
            [
                'name' => 'افزودن خدمت نظام وظیفه',
                'slug' => '/person/military-service/add/{id}'
            ],
            [
                'name' => 'افزودن ایثارگری',
                'slug' => '/person/isar/add/{id}'
            ],
            [
                'name' => 'بروزرسانی اطلاعات تماس',
                'slug' => '/person/contact-data/update/{id}'
            ]
        ]);

        $dataToInsert = $apiRoutes->map(function ($route) {

            return [
                'name' => $route['name'],
                'slug' => $route['slug'],
                'module_id' => 10,
                'permission_type_id' => 2
            ];
        })->toArray();

        \DB::transaction(function () use ($dataToInsert) {
            Permission::upsert($dataToInsert, ['slug']);

        });

    }
}
