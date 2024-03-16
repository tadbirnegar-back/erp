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
}
