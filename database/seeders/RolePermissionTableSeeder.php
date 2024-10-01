<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolePermissionTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('role_permission')->delete();

        \DB::table('role_permission')->upsert(array(
            0 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '1',
                ),
            1 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '2',
                ),
            2 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '3',
                ),
            3 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '4',
                ),
            4 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '5',
                ),
            5 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '6',
                ),
            6 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '7',
                ),
            7 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '8',
                ),
            8 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '9',
                ),
            9 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '10',
                ),
            10 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '11',
                ),
            11 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '12',
                ),
            12 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '13',
                ),
            13 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '14',
                ),
            14 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '15',
                ),
            15 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '16',
                ),
            16 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '17',
                ),
            17 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '18',
                ),
            18 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '48',
                ),
            19 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '49',
                ),
            20 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '50',
                ),
            21 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '51',
                ),
            22 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '52',
                ),
            23 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '53',
                ),
            24 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '54',
                ),
            25 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '63',
                ),
            26 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '64',
                ),
            27 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '65',
                ),
            28 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '66',
                ),
            29 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '67',
                ),
            30 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '68',
                ),
            31 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '69',
                ),
            32 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '70',
                ),
            33 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '71',
                ),
            34 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '72',
                ),
            35 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '73',
                ),
            36 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '74',
                ),
            37 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '75',
                ),
            38 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '76',
                ),
            39 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '77',
                ),
            40 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '78',
                ),
            41 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '79',
                ),
            42 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '80',
                ),
            43 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '81',
                ),
            44 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '82',
                ),
            45 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '83',
                ),
            46 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '84',
                ),
            47 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '85',
                ),
            48 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '86',
                ),
            49 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '87',
                ),
            50 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '88',
                ),
            51 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '89',
                ),
            52 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '90',
                ),
            53 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '91',
                ),
            54 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '92',
                ),
            55 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '93',
                ),
            56 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '94',
                ),
            57 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '95',
                ),
            58 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '96',
                ),
            59 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '97',
                ),
            60 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '98',
                ),
            61 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '99',
                ),
            62 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '100',
                ),
            63 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '13',
                ),
            64 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '16',
                ),
            65 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '14',
                ),
            66 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '17',
                ),
            67 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '15',
                ),
            68 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '18',
                ),
            69 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '48',
                ),
            70 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '54',
                ),
            71 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '52',
                ),
            72 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '49',
                ),
            73 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '50',
                ),
            74 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '51',
                ),
            75 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '116',
                ),
            76 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '116',
                ),
            77 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '1',
                ),
            78 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '2',
                ),
            79 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '3',
                ),
            80 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '4',
                ),
            81 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '5',
                ),
            82 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '6',
                ),
            83 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '7',
                ),
            84 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '8',
                ),
            85 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '9',
                ),
            86 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '10',
                ),
            87 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '11',
                ),
            88 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '12',
                ),
            89 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '116',
                ),
            90 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '13',
                ),
            91 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '14',
                ),
            92 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '15',
                ),
            93 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '18',
                ),
            94 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '17',
                ),
            95 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '16',
                ),
            96 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '45',
                ),
            97 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '46',
                ),
            98 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '47',
                ),
            99 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '48',
                ),
            100 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '49',
                ),
            101 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '50',
                ),
            102 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '52',
                ),
            103 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '54',
                ),
            104 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '51',
                ),
            105 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '63',
                ),
            106 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '64',
                ),
            107 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '65',
                ),
            108 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '66',
                ),
            109 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '67',
                ),
            110 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '68',
                ),
            111 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '71',
                ),
            112 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '70',
                ),
            113 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '69',
                ),
            114 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '72',
                ),
            115 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '75',
                ),
            116 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '76',
                ),
            117 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '77',
                ),
            118 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '74',
                ),
            119 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '73',
                ),
            120 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '78',
                ),
            121 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '81',
                ),
            122 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '79',
                ),
            123 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '82',
                ),
            124 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '80',
                ),
            125 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '83',
                ),
            126 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '84',
                ),
            127 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '85',
                ),
            128 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '86',
                ),
            129 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '87',
                ),
            130 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '88',
                ),
            131 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '118',
                ),
            132 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '119',
                ),
            133 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '119',
                ),
            134 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '120',
                ),
            135 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '121',
                ),
            136 =>
                array(
                    'role_id' => '1',
                    'permission_id' => '122',
                ),
            137 =>
                array(
                    'role_id' => '2',
                    'permission_id' => '122',
                ),
            138 =>
                array(
                    'role_id' => '3',
                    'permission_id' => '122',
                ),
            139 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '13',
                ),
            140 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '16',
                ),
            141 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '14',
                ),
            142 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '17',
                ),
            143 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '15',
                ),
            144 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '18',
                ),
            145 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '116',
                ),
            146 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '119',
                ),
            147 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '48',
                ),
            148 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '54',
                ),
            149 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '49',
                ),
            150 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '52',
                ),
            151 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '50',
                ),
            152 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '51',
                ),
            153 =>
                array(
                    'role_id' => '4',
                    'permission_id' => '116',
                ),
            154 =>
                array(
                    'role_id' => '5',
                    'permission_id' => '48',
                ),
            155 =>
                array(
                    'role_id' => '5',
                    'permission_id' => '49',
                ),
            156 =>
                array(
                    'role_id' => '5',
                    'permission_id' => '50',
                ),
            157 =>
                array(
                    'role_id' => '5',
                    'permission_id' => '52',
                ),
            158 =>
                array(
                    'role_id' => '5',
                    'permission_id' => '54',
                ),
            159 =>
                array(
                    'role_id' => '5',
                    'permission_id' => '51',
                ),
            160 =>
                array(
                    'role_id' => '6',
                    'permission_id' => '48',
                ),
            161 =>
                array(
                    'role_id' => '6',
                    'permission_id' => '49',
                ),
            162 =>
                array(
                    'role_id' => '6',
                    'permission_id' => '50',
                ),
            163 =>
                array(
                    'role_id' => '6',
                    'permission_id' => '52',
                ),
            164 =>
                array(
                    'role_id' => '6',
                    'permission_id' => '54',
                ),
            165 =>
                array(
                    'role_id' => '6',
                    'permission_id' => '51',
                ),
            166 =>
                array(
                    'role_id' => '7',
                    'permission_id' => '54',
                ),
            167 =>
                array(
                    'role_id' => '7',
                    'permission_id' => '51',
                ),
            168 =>
                array(
                    'role_id' => '7',
                    'permission_id' => '52',
                ),
            169 =>
                array(
                    'role_id' => '7',
                    'permission_id' => '50',
                ),
            170 =>
                array(
                    'role_id' => '7',
                    'permission_id' => '49',
                ),
            171 =>
                array(
                    'role_id' => '7',
                    'permission_id' => '48',
                ),
            172 =>
                array(
                    'role_id' => '8',
                    'permission_id' => '48',
                ),
            173 =>
                array(
                    'role_id' => '8',
                    'permission_id' => '49',
                ),
            174 =>
                array(
                    'role_id' => '8',
                    'permission_id' => '50',
                ),
            175 =>
                array(
                    'role_id' => '8',
                    'permission_id' => '52',
                ),
            176 =>
                array(
                    'role_id' => '8',
                    'permission_id' => '54',
                ),
            177 =>
                array(
                    'role_id' => '8',
                    'permission_id' => '51',
                ),
        ), ['permission_id', 'role_id']);


    }
}
