<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\Level;

trait LevelTrait
{
    public function index()
    {
        $result = Level::all();

        return $result;
    }

    public function store(array $data)
    {

        $level = new Level;
        $level->name = $data['levelName'];

        $status = $this->activeLevelStatus();

        $level->status_id = $status->id;
        $level->save();
        return $level;


    }

    public function update(array $data, int $ID)
    {

            $level = Level::findOrFail($ID);
            $level->name = $data['levelName'];
            $level->save();
            return $level;

    }

    public function show(int $ID)
    {
        return Level::findOrFail($ID);
    }

    public function activeLevelStatus()
    {
        return Level::GetAllStatuses()->firstWhere('name', '=', 'فعال');
    }
}
