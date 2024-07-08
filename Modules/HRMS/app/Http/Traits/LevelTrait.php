<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\Level;

trait LevelTrait
{
    public function levelIndex()
    {
        $result = Level::all();

        return $result;
    }

    public function storeLevel(array $data)
    {

        $level = new Level;
        $level->name = $data['levelName'];

        $status = $this->activeLevelStatus();

        $level->status_id = $status->id;
        $level->save();
        return $level;


    }

    public function updateLevel( Level $level,array $data)
    {

            $level->name = $data['levelName'];
            $level->save();
            return $level;

    }

    public function showLevel(int $ID)
    {
        return Level::findOrFail($ID);
    }

    public function activeLevelStatus()
    {
        return Level::GetAllStatuses()->firstWhere('name', '=', 'فعال');
    }
}
