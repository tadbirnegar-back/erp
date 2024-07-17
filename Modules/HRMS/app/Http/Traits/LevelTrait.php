<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\Level;

trait LevelTrait
{
     private string $activeLevelName = 'فعال';
     private string $inactiveLevelName = 'غیرفعال';
    public function levelIndex()
    {
        $result = Level::whereHas('status', function ($query) {
            $query->where('name', '=', $this->activeLevelName);
        })->get();

        return $result;
    }

    public function storeLevel(array $data)
    {

        $level = new Level;
        $level->name = $data['title'];

        $status = $this->activeLevelStatus();

        $level->status_id = $status->id;
        $level->save();
        return $level;


    }

    public function updateLevel( Level $level,array $data)
    {

            $level->name = $data['title'];
            $level->save();
            return $level;

    }

    public function showLevel(int $ID)
    {
        return Level::findOrFail($ID);
    }

    public function activeLevelStatus()
    {
        return Level::GetAllStatuses()->firstWhere('name', '=', $this->activeLevelName);
    }
    public function inactiveLevelStatus()
    {
        return Level::GetAllStatuses()->firstWhere('name', '=', $this->inactiveLevelName);
    }
}
