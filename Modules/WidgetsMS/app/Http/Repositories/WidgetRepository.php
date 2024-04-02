<?php

namespace Modules\WidgetsMS\app\Http\Repositories;

use Modules\WidgetsMS\app\Models\Widget;

class WidgetRepository
{


    public function store(array $data)
    {
        try {
            \DB::beginTransaction();

            $widget = new Widget();
            $widget->permission_id = $data['permissionID'];
            $widget->user_id = $data['userID'];
            $widget->isActivated = $data['activation'] ? 1 : 0;
            $widget->save();

            \DB::commit();

            return $widget;
        } catch (\Exception $e) {
            \DB::rollBack();

            return $e;
        }

    }

    public static function extractor(array $targetURIs)
    {
        $routes = \Route::getRoutes()->getRoutes();
        $matchingRoutes = [];

        foreach ($targetURIs as $targetURI) {
            foreach ($routes as $route) {
                if (str_contains($route->uri(),$targetURI)) {
                    $action = $route->getAction();
                    $matchingRoutes[$targetURI] = [
                        'controller' => explode('@', $action['controller'])[0],
                        'method' => $route->getActionMethod(),
                    ];
                    break; // Exit inner loop after finding a match for the current targetURI
                }
            }
        }

        return $matchingRoutes;
    }
}
