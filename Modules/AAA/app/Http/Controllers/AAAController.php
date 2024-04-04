<?php

namespace Modules\AAA\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DateTime;
use GuzzleHttp\Client;
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshTokenRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\AAA\app\Models\User;
use Modules\WidgetsMS\app\Http\Repositories\WidgetRepository;
use Modules\WidgetsMS\app\Models\Widget;
use Str;
use Symfony\Component\HttpFoundation\Cookie;


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
