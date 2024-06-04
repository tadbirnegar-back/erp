<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('statuses')->delete();
        
        \DB::table('statuses')->insert(array (
            0 => 
            array (
                'id' => 46,
                'name' => 'پرداخت شده',
                'model' => 'Modules\\Gateway\\app\\Models\\Payment',
            ),
            1 => 
            array (
                'id' => 47,
                'name' => 'پرداخت ناموفق',
                'model' => 'Modules\\Gateway\\app\\Models\\Payment',
            ),
            2 => 
            array (
                'id' => 26,
                'name' => 'پیش خرید',
                'model' => 'Modules\\Merchandise\\app\\Models\\MerchandiseProduct',
            ),
            3 => 
            array (
                'id' => 25,
                'name' => 'تماس بگیرید',
                'model' => 'Modules\\Merchandise\\app\\Models\\MerchandiseProduct',
            ),
            4 => 
            array (
                'id' => 45,
                'name' => 'در انتظار پرداخت',
                'model' => 'Modules\\Gateway\\app\\Models\\Payment',
            ),
            5 => 
            array (
                'id' => 14,
                'name' => 'غیرفعال',
                'model' => 'Modules\\AAA\\app\\Models\\Role',
            ),
            6 => 
            array (
                'id' => 2,
                'name' => 'غیرفعال',
                'model' => 'Modules\\AAA\\app\\Models\\User',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'غیرفعال',
                'model' => 'Modules\\AddressMS\\app\\Models\\Address',
            ),
            8 => 
            array (
                'id' => 4,
                'name' => 'غیرفعال',
                'model' => 'Modules\\BranchMS\\app\\Models\\Branch',
            ),
            9 => 
            array (
                'id' => 12,
                'name' => 'غیرفعال',
                'model' => 'Modules\\CustomerMS\\app\\Models\\Customer',
            ),
            10 => 
            array (
                'id' => 6,
                'name' => 'غیرفعال',
                'model' => 'Modules\\FileMS\\app\\Models\\File',
            ),
            11 => 
            array (
                'id' => 38,
                'name' => 'غیرفعال',
                'model' => 'Modules\\FormGMS\\app\\Models\\Field',
            ),
            12 => 
            array (
                'id' => 36,
                'name' => 'غیرفعال',
                'model' => 'Modules\\FormGMS\\app\\Models\\Form',
            ),
            13 => 
            array (
                'id' => 40,
                'name' => 'غیرفعال',
                'model' => 'Modules\\FormGMS\\app\\Models\\Option',
            ),
            14 => 
            array (
                'id' => 28,
                'name' => 'غیرفعال',
                'model' => 'Modules\\HRMS\\app\\Models\\Level',
            ),
            15 => 
            array (
                'id' => 30,
                'name' => 'غیرفعال',
                'model' => 'Modules\\HRMS\\app\\Models\\Position',
            ),
            16 => 
            array (
                'id' => 44,
                'name' => 'غیرفعال',
                'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
            ),
            17 => 
            array (
                'id' => 32,
                'name' => 'غیرفعال',
                'model' => 'Modules\\HRMS\\app\\Models\\Skill',
            ),
            18 => 
            array (
                'id' => 34,
                'name' => 'غیرفعال',
                'model' => 'Modules\\HRMS\\app\\Models\\WorkForce',
            ),
            19 => 
            array (
                'id' => 42,
                'name' => 'غیرفعال',
                'model' => 'Modules\\OUnitMS\\app\\Models\\OrganizationUnit',
            ),
            20 => 
            array (
                'id' => 10,
                'name' => 'غیرفعال',
                'model' => 'Modules\\PersonMS\\app\\Models\\Person',
            ),
            21 => 
            array (
                'id' => 16,
                'name' => 'غیرفعال',
                'model' => 'Modules\\ProductMS\\app\\Models\\Product',
            ),
            22 => 
            array (
                'id' => 22,
                'name' => 'غیرفعال',
                'model' => 'Modules\\ProductMS\\app\\Models\\ProductCategory',
            ),
            23 => 
            array (
                'id' => 20,
                'name' => 'غیرفعال',
                'model' => 'Modules\\ProductMS\\app\\Models\\Variant',
            ),
            24 => 
            array (
                'id' => 18,
                'name' => 'غیرفعال',
                'model' => 'Modules\\ProductMS\\app\\Models\\VariantGroup',
            ),
            25 => 
            array (
                'id' => 13,
                'name' => 'فعال',
                'model' => 'Modules\\AAA\\app\\Models\\Role',
            ),
            26 => 
            array (
                'id' => 1,
                'name' => 'فعال',
                'model' => 'Modules\\AAA\\app\\Models\\User',
            ),
            27 => 
            array (
                'id' => 7,
                'name' => 'فعال',
                'model' => 'Modules\\AddressMS\\app\\Models\\Address',
            ),
            28 => 
            array (
                'id' => 3,
                'name' => 'فعال',
                'model' => 'Modules\\BranchMS\\app\\Models\\Branch',
            ),
            29 => 
            array (
                'id' => 11,
                'name' => 'فعال',
                'model' => 'Modules\\CustomerMS\\app\\Models\\Customer',
            ),
            30 => 
            array (
                'id' => 5,
                'name' => 'فعال',
                'model' => 'Modules\\FileMS\\app\\Models\\File',
            ),
            31 => 
            array (
                'id' => 37,
                'name' => 'فعال',
                'model' => 'Modules\\FormGMS\\app\\Models\\Field',
            ),
            32 => 
            array (
                'id' => 35,
                'name' => 'فعال',
                'model' => 'Modules\\FormGMS\\app\\Models\\Form',
            ),
            33 => 
            array (
                'id' => 39,
                'name' => 'فعال',
                'model' => 'Modules\\FormGMS\\app\\Models\\Option',
            ),
            34 => 
            array (
                'id' => 27,
                'name' => 'فعال',
                'model' => 'Modules\\HRMS\\app\\Models\\Level',
            ),
            35 => 
            array (
                'id' => 29,
                'name' => 'فعال',
                'model' => 'Modules\\HRMS\\app\\Models\\Position',
            ),
            36 => 
            array (
                'id' => 43,
                'name' => 'فعال',
                'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
            ),
            37 => 
            array (
                'id' => 31,
                'name' => 'فعال',
                'model' => 'Modules\\HRMS\\app\\Models\\Skill',
            ),
            38 => 
            array (
                'id' => 33,
                'name' => 'فعال',
                'model' => 'Modules\\HRMS\\app\\Models\\WorkForce',
            ),
            39 => 
            array (
                'id' => 41,
                'name' => 'فعال',
                'model' => 'Modules\\OUnitMS\\app\\Models\\OrganizationUnit',
            ),
            40 => 
            array (
                'id' => 9,
                'name' => 'فعال',
                'model' => 'Modules\\PersonMS\\app\\Models\\Person',
            ),
            41 => 
            array (
                'id' => 15,
                'name' => 'فعال',
                'model' => 'Modules\\ProductMS\\app\\Models\\Product',
            ),
            42 => 
            array (
                'id' => 21,
                'name' => 'فعال',
                'model' => 'Modules\\ProductMS\\app\\Models\\ProductCategory',
            ),
            43 => 
            array (
                'id' => 19,
                'name' => 'فعال',
                'model' => 'Modules\\ProductMS\\app\\Models\\Variant',
            ),
            44 => 
            array (
                'id' => 17,
                'name' => 'فعال',
                'model' => 'Modules\\ProductMS\\app\\Models\\VariantGroup',
            ),
            45 => 
            array (
                'id' => 23,
                'name' => 'موجود',
                'model' => 'Modules\\Merchandise\\app\\Models\\MerchandiseProduct',
            ),
            46 => 
            array (
                'id' => 24,
                'name' => 'ناموجود',
                'model' => 'Modules\\Merchandise\\app\\Models\\MerchandiseProduct',
            ),
        ));
        
        
    }
}