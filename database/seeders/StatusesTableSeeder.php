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


//        \DB::table('statuses')->delete();

        \DB::table('statuses')->upsert(array(
            0 =>
                array(
                    'id' => '1',
                    'name' => 'فعال',
                    'model' => 'Modules\\AAA\\app\\Models\\User',
                    'class_name' => NULL,
                ),
            1 =>
                array(
                    'id' => '2',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\AAA\\app\\Models\\User',
                    'class_name' => NULL,
                ),
            2 =>
                array(
                    'id' => '3',
                    'name' => 'فعال',
                    'model' => 'Modules\\BranchMS\\app\\Models\\Branch',
                    'class_name' => NULL,
                ),
            3 =>
                array(
                    'id' => '4',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\BranchMS\\app\\Models\\Branch',
                    'class_name' => NULL,
                ),
            4 =>
                array(
                    'id' => '5',
                    'name' => 'فعال',
                    'model' => 'Modules\\FileMS\\app\\Models\\File',
                    'class_name' => NULL,
                ),
            5 =>
                array(
                    'id' => '6',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\FileMS\\app\\Models\\File',
                    'class_name' => NULL,
                ),
            6 =>
                array(
                    'id' => '7',
                    'name' => 'فعال',
                    'model' => 'Modules\\AddressMS\\app\\Models\\Address',
                    'class_name' => NULL,
                ),
            7 =>
                array(
                    'id' => '8',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\AddressMS\\app\\Models\\Address',
                    'class_name' => NULL,
                ),
            8 =>
                array(
                    'id' => '9',
                    'name' => 'فعال',
                    'model' => 'Modules\\PersonMS\\app\\Models\\Person',
                    'class_name' => NULL,
                ),
            9 =>
                array(
                    'id' => '10',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\PersonMS\\app\\Models\\Person',
                    'class_name' => NULL,
                ),
            10 =>
                array(
                    'id' => '11',
                    'name' => 'فعال',
                    'model' => 'Modules\\CustomerMS\\app\\Models\\Customer',
                    'class_name' => NULL,
                ),
            11 =>
                array(
                    'id' => '12',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\CustomerMS\\app\\Models\\Customer',
                    'class_name' => NULL,
                ),
            12 =>
                array(
                    'id' => '13',
                    'name' => 'فعال',
                    'model' => 'Modules\\AAA\\app\\Models\\Role',
                    'class_name' => NULL,
                ),
            13 =>
                array(
                    'id' => '14',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\AAA\\app\\Models\\Role',
                    'class_name' => NULL,
                ),
            14 =>
                array(
                    'id' => '15',
                    'name' => 'فعال',
                    'model' => 'Modules\\ProductMS\\app\\Models\\Product',
                    'class_name' => NULL,
                ),
            15 =>
                array(
                    'id' => '16',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\ProductMS\\app\\Models\\Product',
                    'class_name' => NULL,
                ),
            16 =>
                array(
                    'id' => '17',
                    'name' => 'فعال',
                    'model' => 'Modules\\ProductMS\\app\\Models\\VariantGroup',
                    'class_name' => NULL,
                ),
            17 =>
                array(
                    'id' => '18',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\ProductMS\\app\\Models\\VariantGroup',
                    'class_name' => NULL,
                ),
            18 =>
                array(
                    'id' => '19',
                    'name' => 'فعال',
                    'model' => 'Modules\\ProductMS\\app\\Models\\Variant',
                    'class_name' => NULL,
                ),
            19 =>
                array(
                    'id' => '20',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\ProductMS\\app\\Models\\Variant',
                    'class_name' => NULL,
                ),
            20 =>
                array(
                    'id' => '21',
                    'name' => 'فعال',
                    'model' => 'Modules\\ProductMS\\app\\Models\\ProductCategory',
                    'class_name' => NULL,
                ),
            21 =>
                array(
                    'id' => '22',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\ProductMS\\app\\Models\\ProductCategory',
                    'class_name' => NULL,
                ),
            22 =>
                array(
                    'id' => '23',
                    'name' => 'موجود',
                    'model' => 'Modules\\Merchandise\\app\\Models\\MerchandiseProduct',
                    'class_name' => NULL,
                ),
            23 =>
                array(
                    'id' => '24',
                    'name' => 'ناموجود',
                    'model' => 'Modules\\Merchandise\\app\\Models\\MerchandiseProduct',
                    'class_name' => NULL,
                ),
            24 =>
                array(
                    'id' => '25',
                    'name' => 'تماس بگیرید',
                    'model' => 'Modules\\Merchandise\\app\\Models\\MerchandiseProduct',
                    'class_name' => NULL,
                ),
            25 =>
                array(
                    'id' => '26',
                    'name' => 'پیش خرید',
                    'model' => 'Modules\\Merchandise\\app\\Models\\MerchandiseProduct',
                    'class_name' => NULL,
                ),
            26 =>
                array(
                    'id' => '27',
                    'name' => 'فعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\Level',
                    'class_name' => NULL,
                ),
            27 =>
                array(
                    'id' => '28',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\Level',
                    'class_name' => NULL,
                ),
            28 =>
                array(
                    'id' => '29',
                    'name' => 'فعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\Position',
                    'class_name' => NULL,
                ),
            29 =>
                array(
                    'id' => '30',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\Position',
                    'class_name' => NULL,
                ),
            30 =>
                array(
                    'id' => '31',
                    'name' => 'فعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\Skill',
                    'class_name' => NULL,
                ),
            31 =>
                array(
                    'id' => '32',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\Skill',
                    'class_name' => NULL,
                ),
            32 =>
                array(
                    'id' => '33',
                    'name' => 'فعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\Employee',
                    'class_name' => NULL,
                ),
            33 =>
                array(
                    'id' => '34',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\Employee',
                    'class_name' => NULL,
                ),
            34 =>
                array(
                    'id' => '35',
                    'name' => 'فعال',
                    'model' => 'Modules\\FormGMS\\app\\Models\\Form',
                    'class_name' => NULL,
                ),
            35 =>
                array(
                    'id' => '36',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\FormGMS\\app\\Models\\Form',
                    'class_name' => NULL,
                ),
            36 =>
                array(
                    'id' => '37',
                    'name' => 'فعال',
                    'model' => 'Modules\\FormGMS\\app\\Models\\Field',
                    'class_name' => NULL,
                ),
            37 =>
                array(
                    'id' => '38',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\FormGMS\\app\\Models\\Field',
                    'class_name' => NULL,
                ),
            38 =>
                array(
                    'id' => '39',
                    'name' => 'فعال',
                    'model' => 'Modules\\FormGMS\\app\\Models\\Option',
                    'class_name' => NULL,
                ),
            39 =>
                array(
                    'id' => '40',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\FormGMS\\app\\Models\\Option',
                    'class_name' => NULL,
                ),
            40 =>
                array(
                    'id' => '41',
                    'name' => 'فعال',
                    'model' => 'Modules\\OUnitMS\\app\\Models\\OrganizationUnit',
                    'class_name' => NULL,
                ),
            41 =>
                array(
                    'id' => '42',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\OUnitMS\\app\\Models\\OrganizationUnit',
                    'class_name' => NULL,
                ),
            42 =>
                array(
                    'id' => '43',
                    'name' => 'فعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
                    'class_name' => 'success',
                ),
            43 =>
                array(
                    'id' => '44',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
                    'class_name' => 'danger',
                ),
            44 =>
                array(
                    'id' => '45',
                    'name' => 'در انتظار پرداخت',
                    'model' => 'Modules\\Gateway\\app\\Models\\Payment',
                    'class_name' => NULL,
                ),
            45 =>
                array(
                    'id' => '46',
                    'name' => 'پرداخت شده',
                    'model' => 'Modules\\Gateway\\app\\Models\\Payment',
                    'class_name' => NULL,
                ),
            46 =>
                array(
                    'id' => '47',
                    'name' => 'پرداخت ناموفق',
                    'model' => 'Modules\\Gateway\\app\\Models\\Payment',
                    'class_name' => NULL,
                ),
            47 =>
                array(
                    'id' => '48',
                    'name' => 'فعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\ScriptAgentType',
                    'class_name' => NULL,
                ),
            48 =>
                array(
                    'id' => '49',
                    'name' => 'حذف شده',
                    'model' => 'Modules\\HRMS\\app\\Models\\ScriptAgentType',
                    'class_name' => NULL,
                ),
            49 =>
                array(
                    'id' => '50',
                    'name' => 'فعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\Job',
                    'class_name' => NULL,
                ),
            50 =>
                array(
                    'id' => '51',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\Job',
                    'class_name' => NULL,
                ),
            51 =>
                array(
                    'id' => '54',
                    'name' => 'فعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\HireType',
                    'class_name' => NULL,
                ),
            52 =>
                array(
                    'id' => '55',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\HireType',
                    'class_name' => NULL,
                ),
            53 =>
                array(
                    'id' => '56',
                    'name' => 'فعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\ScriptType',
                    'class_name' => NULL,
                ),
            54 =>
                array(
                    'id' => '57',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\ScriptType',
                    'class_name' => NULL,
                ),
            55 =>
                array(
                    'id' => '58',
                    'name' => 'فعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\ScriptAgent',
                    'class_name' => NULL,
                ),
            56 =>
                array(
                    'id' => '59',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\HRMS\\app\\Models\\ScriptAgent',
                    'class_name' => NULL,
                ),
            57 =>
                array(
                    'id' => '60',
                    'name' => 'در انتظار تایید',
                    'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
                    'class_name' => 'primary',
                ),
            58 =>
                array(
                    'id' => '61',
                    'name' => 'درانتظار تایید من',
                    'model' => 'Modules\\HRMS\\app\\Models\\ScriptApprovingList',
                    'class_name' => 'primary',
                ),
            59 =>
                array(
                    'id' => '62',
                    'name' => 'درانتظار تایید',
                    'model' => 'Modules\\HRMS\\app\\Models\\ScriptApprovingList',
                    'class_name' => 'primary',
                ),
            60 =>
                array(
                    'id' => '63',
                    'name' => 'تایید شده',
                    'model' => 'Modules\\HRMS\\app\\Models\\ScriptApprovingList',
                    'class_name' => 'success',
                ),
            61 =>
                array(
                    'id' => '64',
                    'name' => 'رد شده',
                    'model' => 'Modules\\HRMS\\app\\Models\\ScriptApprovingList',
                    'class_name' => 'danger',
                ),
            62 =>
                array(
                    'id' => '65',
                    'name' => 'در انتظار تایید',
                    'model' => 'Modules\\HRMS\\app\\Models\\Employee',
                    'class_name' => NULL,
                ),
            63 =>
                array(
                    'id' => '66',
                    'name' => 'تکمیل شده',
                    'model' => 'Modules\\EMS\\app\\Models\\Enactment',
                    'class_name' => NULL,
                ),
            64 =>
                array(
                    'id' => '67',
                    'name' => 'در انتظار بررسی هیئت',
                    'model' => 'Modules\\EMS\\app\\Models\\Enactment',
                    'class_name' => NULL,
                ),
            65 =>
                array(
                    'id' => '68',
                    'name' => 'در انتظار وصول',
                    'model' => 'Modules\\EMS\\app\\Models\\Enactment',
                    'class_name' => NULL,
                ),
            66 =>
                array(
                    'id' => '69',
                    'name' => 'لغو شده',
                    'model' => 'Modules\\EMS\\app\\Models\\Enactment',
                    'class_name' => NULL,
                ),
            67 =>
                array(
                    'id' => '70',
                    'name' => 'پیش نویس',
                    'model' => 'Modules\\EMS\\app\\Models\\Meeting',
                    'class_name' => NULL,
                ),
            68 =>
                array(
                    'id' => '71',
                    'name' => 'تایید شده',
                    'model' => 'Modules\\EMS\\app\\Models\\Meeting',
                    'class_name' => NULL,
                ),
            69 =>
                array(
                    'id' => '72',
                    'name' => 'اتمام جلسه',
                    'model' => 'Modules\\EMS\\app\\Models\\Meeting',
                    'class_name' => NULL,
                ),
            70 =>
                array(
                    'id' => '73',
                    'name' => 'لغو شده',
                    'model' => 'Modules\\EMS\\app\\Models\\Meeting',
                    'class_name' => NULL,
                ),
            71 =>
                array(
                    'id' => '74',
                    'name' => 'برگزار شده',
                    'model' => 'Modules\\EMS\\app\\Models\\Meeting',
                    'class_name' => NULL,
                ),
            72 =>
                array(
                    'id' => '75',
                    'name' => 'فعال',
                    'model' => 'Modules\\EMS\\app\\Models\\MeetingType',
                    'class_name' => NULL,
                ),
            73 =>
                array(
                    'id' => '76',
                    'name' => 'غیرفعال',
                    'model' => 'Modules\\EMS\\app\\Models\\MeetingType',
                    'class_name' => NULL,
                ),
            74 =>
                array(
                    'id' => '77',
                    'name' => 'مغایرت',
                    'model' => 'Modules\\EMS\\app\\Models\\EnactmentReview',
                    'class_name' => 'danger',
                ),
            75 =>
                array(
                    'id' => '78',
                    'name' => 'عدم مغایرت',
                    'model' => 'Modules\\EMS\\app\\Models\\EnactmentReview',
                    'class_name' => 'success',
                ),
            76 =>
                array(
                    'id' => '79',
                    'name' => 'عدم مغایرت سیستمی',
                    'model' => 'Modules\\EMS\\app\\Models\\EnactmentReview',
                    'class_name' => 'success',
                ),
            77 =>
                array(
                    'id' => '80',
                    'name' => 'نامشخص',
                    'model' => 'Modules\\EMS\\app\\Models\\EnactmentReview',
                    'class_name' => 'warning',
                ),
            78 =>
                array(
                    'id' => '81',
                    'name' => 'فعال',
                    'model' => 'Modules\\EMS\\app\\Models\\EnactmentTitle',
                    'class_name' => NULL,
                ),
            79 =>
                array(
                    'id' => '82',
                    'name' => 'حذف شده',
                    'model' => 'Modules\\EMS\\app\\Models\\EnactmentTitle',
                    'class_name' => NULL,
                ),
            80 =>
                array(
                    'id' => '83',
                    'name' => 'منقضی شده',
                    'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
                    'class_name' => 'danger',
                ),
            81 =>
                array(
                    'id' => '84',
                    'name' => 'رد شده',
                    'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
                    'class_name' => 'danger',
                ),
            82 =>
                array(
                    'id' => '85',
                    'name' => 'عزل شده',
                    'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
                    'class_name' => 'danger',
                ),
            83 =>
                array(
                    'id' => '86',
                    'name' => 'پایان خدمت',
                    'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
                    'class_name' => 'danger',
                ),
            84 =>
                array(
                    'id' => '87',
                    'name' => 'باطل شده',
                    'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
                    'class_name' => 'danger',
                ),
            85 =>
                array(
                    'id' => '88',
                    'name' => 'رد شده',
                    'model' => 'Modules\\EMS\\app\\Models\\Enactment',
                    'class_name' => NULL,
                ),
            86 =>
                array(
                    'id' => '89',
                    'name' => 'در انتظار برگزاری جلسه هیئت',
                    'model' => 'Modules\\EMS\\app\\Models\\Enactment',
                    'class_name' => NULL,
                ),
            87 =>
                array(
                    'id' => '90',
                    'name' => 'در انتظار عزل',
                    'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
                    'class_name' => 'danger',
                ),
            88 =>
                array(
                    'id' => '91',
                    'name' => 'قطع همکاری',
                    'model' => 'Modules\\HRMS\\app\\Models\\RecruitmentScript',
                    'class_name' => 'danger',
                ),
        ), ['id']);


    }
}
