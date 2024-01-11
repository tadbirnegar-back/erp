<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\FileMS\app\Models\File;
use Modules\StatusMS\app\Models\Status;

class testController extends Controller
{
    public function run(): void
    {
//        $status = Status::where('name', '=', 'فعال')->where('model','=',File::class)->first();
        $user = User::find(1);
//        $files = $user->files()->with(['statuses' => function ($query) {
//            // Filter statuses to get the latest "active" status for each file
//            $query->latest('created_date')->where('name', 'فعال');
//        }])->get();

//        $files = $user->files()->whereHas('currentStatus', function ($query) {
//            $query->where('name', 'فعال');
//        })->get();
        $files = $user->files()->whereHas('statuses', function ($query) {
            $query->where('name', 'فعال')->latest('created_date');
        })->get();
        dd($files);
        //        dd($status);
//        dd(File::class);
//        dd(File::GetAllStatuses());
//        $permissionTypesData = json_decode(file_get_contents(realpath(__DIR__.'/../../../modules_statuses.json')), true);
//        dd($permissionTypesData);
//        $a = DB::table('permission_types')->where('name', '=', 'sidebar')->get('id')->first();

//        dd(json_decode(file_get_contents(realpath(__DIR__.'/fileTypes.json')), true));

    }
}
