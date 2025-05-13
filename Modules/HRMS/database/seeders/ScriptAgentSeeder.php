<?php

namespace Modules\HRMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\HRMS\app\Http\Enums\FormulaEnum;
use Modules\HRMS\app\Models\ScriptAgent;

class ScriptAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agents = collect(FormulaEnum::cases())->map(function ($agent) {
            return $agent->getLabel();
        });

        try {
            \DB::beginTransaction();
            foreach ($agents as $agent) {
                ScriptAgent::insertOrIgnore([
                    'title' => $agent,
                    'script_agent_type_id' => 11,
                    'status_id' => 58,
                ]);
            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
        }
    }
}
