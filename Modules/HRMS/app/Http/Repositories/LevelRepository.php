<?php

namespace Modules\HRMS\app\Http\Repositories;

use Modules\HRMS\app\Models\Level;

class LevelRepository
{
    protected Level $level;

    public function __construct(Level $level)
    {
        $this->level = $level;
    }

    public function index()
    {
        $result = $this->level::all();

        return $result;
    }

    public function store(array $data)
    {
        /**
         * @var Level $level
         */
        try {
            \DB::beginTransaction();
            $level = new $this->level();
            $level->name = $data['levelName'];
            $status = $this->level::GetAllStatuses()->where('name', '=', 'فعال')->first();

            $level->status_id = $status->id;
            $level->save();
            \DB::commit();
            return $level;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }

    }

    public function update(array $data, int $ID)
    {
        try {
            \DB::beginTransaction();
            $level = $this->level::findOrFail($ID);
            $level->name = $data['levelName'];
//            $level->status_id = $data['statusID'];
            $level->save();
            \DB::commit();
            return $level;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }
    }

    public function show(int $ID)
    {
        return $this->level::findOrFail($ID);
    }
}
