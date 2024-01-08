<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class testController extends Controller
{
    public function run(): void
    {
//        $permissionTypesData = json_decode(file_get_contents(realpath(__DIR__.'/../../../modules_statuses.json')), true);
//        dd($permissionTypesData);
        $a = DB::table('permission_types')->where('name', '=', 'sidebar')->get('id')->first();

        dd($a->id);

    }
}
