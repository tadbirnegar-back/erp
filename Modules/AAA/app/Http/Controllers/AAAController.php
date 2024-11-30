<?php

namespace Modules\AAA\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\WidgetsMS\app\Http\Repositories\WidgetRepository;
use Str;


class AAAController extends Controller
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
    public function update(Request $request, $id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    public function activeWidgets()
    {
        $user = \Auth::user();

//        $user->load('activeWidgets');

//        $user = User::find(2119);


        $activeWidgets = $user->activeWidgets;

        $allPermissions = $activeWidgets->map(function ($widget) {
            return $widget->permission->slug; // Extract permission model
        });

        $functions = WidgetRepository::extractor($allPermissions->toArray());

        $widgetData = [];
        foreach ($functions as $key => $item) {

            $widgetData[] = [
                'name' => Str::replace('/', '', $key),
                'data' => call_user_func([$item['controller'], $item['method']
                    ]
                    , $user)];
        }

        return response()->json($widgetData);

    }

    public function widgets()
    {
        $user = \Auth::user();
//        $user->load('widgets');


        return response()->json($user->widgets);


    }


    // --------------------------------------------------------------------


}
