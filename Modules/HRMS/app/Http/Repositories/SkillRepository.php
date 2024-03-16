<?php

namespace Modules\HRMS\app\Http\Repositories;

use Modules\HRMS\app\Models\Skill;

class SkillRepository
{
    protected Skill $skill;

    public function __construct(Skill $skill)
    {
        $this->skill = $skill;
    }

    public function index()
    {
        $result = $this->skill::all();

        return $result;
    }

    public function store(array $data)
    {

        try {

            \DB::beginTransaction();
            /**
             * @var Skill $skill
             */
            $skill = new $this->skill();
            $skill->name = $data['skillName'];
            $status = $this->skill::GetAllStatuses()->where('name', '=', 'فعال')->first();
            $skill->status_id = $status->id;;
            $skill->save();
            \DB::commit();
            return $skill;

        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }

    }

    public function show(int $id)
    {
        return $this->skill::findOrFail($id);
    }

    public function update(array $data, int $id)
    {
        try {

            \DB::beginTransaction();
            /**
             * @var Skill $skill
             */
            $skill =  $this->skill::findOrFail($id);

            $skill->name = $data['skillName'];
//            $skill->status_id = $data['statusID'];
            $skill->save();
            \DB::commit();
            return $skill;

        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }
    }


}
