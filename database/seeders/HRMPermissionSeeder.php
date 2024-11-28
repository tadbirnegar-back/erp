<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AAA\app\Models\Permission;

class HRMPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apiRoutes = collect([
            [
                'name' => 'فیلتر لیست کارمندان',
                'slug' => '/hrm/employee/list/filter'
            ],
            [
                'name' => 'پیکربندی منابع انسانی',
                'slug' => '/hrm/setting'
            ],
            [
                'name' => 'جستجوی کارمند',
                'slug' => '/employee/natural/search'
            ],
            [
                'name' => 'جستجوی کد ملی کارمند',
                'slug' => '/employee/national-code/search'
            ],
            [
                'name' => 'لیست استان‌های استخدام',
                'slug' => '/recruitment/list/state_ofc'
            ],
            [
                'name' => 'لیست شهرهای استخدام',
                'slug' => '/recruitment/list/city_ofc'
            ],
            [
                'name' => 'لیست مناطق استخدام',
                'slug' => '/recruitment/list/district_ofc'
            ],
            [
                'name' => 'لیست شهرک‌های استخدام',
                'slug' => '/recruitment/list/town_ofc'
            ],
            [
                'name' => 'لیست روستاهای استخدام',
                'slug' => '/recruitment/list/village_ofc'
            ],
            [
                'name' => 'افزودن نوع عامل حکم',
                'slug' => '/hrm/script-agent-type/add'
            ],
            [
                'name' => 'بروزرسانی نوع عامل حکم',
                'slug' => '/hrm/script-agent-type/update/{id}'
            ],
            [
                'name' => 'حذف نوع عامل حکم',
                'slug' => '/hrm/script-agent-type/delete/{id}'
            ],
            [
                'name' => 'افزودن شغل منابع انسانی',
                'slug' => '/hrm/jobs/add'
            ],
            [
                'name' => 'بروزرسانی شغل منابع انسانی',
                'slug' => '/hrm/jobs/update/{id}'
            ],
            [
                'name' => 'حذف شغل منابع انسانی',
                'slug' => '/hrm/jobs/delete/{id}'
            ],
            [
                'name' => 'افزودن نوع استخدام',
                'slug' => '/hrm/hire-types/add'
            ],
            [
                'name' => 'بروزرسانی نوع استخدام',
                'slug' => '/hrm/hire-types/update/{id}'
            ],
            [
                'name' => 'حذف نوع استخدام',
                'slug' => '/hrm/hire-types/delete/{id}'
            ],
            [
                'name' => 'افزودن نوع حکم',
                'slug' => '/hrm/script-types/add'
            ],
            [
                'name' => 'بروزرسانی نوع حکم',
                'slug' => '/hrm/script-types/update/{id}'
            ],
            [
                'name' => 'حذف نوع حکم',
                'slug' => '/hrm/script-types/delete/{id}'
            ],
            [
                'name' => 'افزودن عامل حکم',
                'slug' => '/hrm/script-agents/add'
            ],
            [
                'name' => 'بروزرسانی عامل حکم',
                'slug' => '/hrm/script-agents/update/{id}'
            ],
            [
                'name' => 'حذف عامل حکم',
                'slug' => '/hrm/script-agents/delete/{id}'
            ],
            [
                'name' => 'لیست موقعیت‌های واحد سازمانی',
                'slug' => '/hrm/ounit/positions/list'
            ],
            [
                'name' => 'ترکیبات حکم کارمند',
                'slug' => '/hrm/employee/script-combos'
            ],
            [
                'name' => 'نوع حکم کارمند',
                'slug' => '/hrm/employee/script-types'
            ],
            [
                'name' => 'احکام صادر شده',
                'slug' => '/hrm/rc/list'
            ],
            [
                'name' => 'لیست منابع انسانی پرچ',
                'slug' => '/hrm/prc/list'
            ],
            [
                'name' => 'جزئیات منابع انسانی پرچ',
                'slug' => '/hrm/prc/{id}'
            ],
            [
                'name' => 'اعطای منابع انسانی',
                'slug' => '/hrm/rc/grant/{id}'
            ],
            [
                'name' => 'افزودن درخواست منابع انسانی',
                'slug' => '/hrm/rc/insert/add'
            ],
            [
                'name' => 'لیست سطح تحصیلات',
                'slug' => '/hrm/education-levels/list'
            ],
            [
                'name' => 'لیست انواع ایثارگران',
                'slug' => '/hrm/isar-types/list'
            ],
            [
                'name' => 'لیست انواع نسبت‌ها',
                'slug' => '/hrm/relative-types/list'
            ],
            [
                'name' => 'ثبت دهیار',
                'slug' => '/hrm/register/dehyar'
            ],
            [
                'name' => 'تأیید کارمند',
                'slug' => '/hrm/employee/verify'
            ],
            [
                'name' => 'تأیید شده‌های منابع انسانی',
                'slug' => '/hrm/verified'
            ],
            [
                'name' => 'تأیید منابع انسانی',
                'slug' => '/hrm/confirm'
            ],
            [
                'name' => 'تأیید اعتبار منابع انسانی',
                'slug' => '/hrm/verify'
            ],
            [
                'name' => 'ویرایش تأییدیه کارمند',
                'slug' => '/hrm/employee/confirm/edit'
            ]
        ]);


        $dataToInsert = $apiRoutes->map(function ($route) {
            if (str_contains($route['slug'], 'add') || str_contains($route['slug'], 'list') || str_contains($route['slug'], 'setting')) {
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
