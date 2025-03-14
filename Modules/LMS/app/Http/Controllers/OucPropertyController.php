<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\LMS\app\Models\OucProperty;
use Modules\LMS\app\Resources\OucPropertyListResource;

class OucPropertyController extends Controller
{
    public function listing(Request $request)
    {
        $data = $request -> all();
        $ids = json_decode($data['ids']);
        $properties = OucProperty::whereIn('ounit_cat_id' , $ids)->select('id' , 'name')
            ->where('name', '!=', 'جمعیت')
            ->get();

        return OucPropertyListResource::collection($properties);
    }
}
