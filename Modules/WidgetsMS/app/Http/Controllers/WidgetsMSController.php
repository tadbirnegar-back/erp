<?php

namespace Modules\WidgetsMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\WidgetsMS\app\Http\Repositories\WidgetRepository;

class WidgetsMSController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {

        $data = $request->all();
        $user = \Auth::user();
        try {
            \DB::beginTransaction();
            $widgets = json_decode($data['widgets'], true);

            $upsert = WidgetRepository::widgetsUpdate($widgets, $user->id);

            \DB::commit();
            return response()->json(['message'=>'با موفقیت ثبت شد']);

        }catch (\Exception $e){
            \DB::rollBack();
//            return response()->json(['message'=>$e->getMessage()]);
            return response()->json(['message'=>'خطا در ثبت تغییرات']);

        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }
}
