<?php

namespace Modules\LMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\LMS\app\Models\OucPropertyValue;

class OucPropertyValueController extends Controller
{
    public function listing(Request $request)
    {
        $data = $request -> all();
        $ids = json_decode($data['ids']);
        $propertyValues = OucPropertyValue::where('ouc_property_id' , $ids)->select('id' , 'value')->get();
        return response() -> json($propertyValues);
    }
}
