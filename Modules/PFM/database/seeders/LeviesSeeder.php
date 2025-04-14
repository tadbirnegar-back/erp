<?php

namespace Modules\PFM\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\LMS\app\Models\AnswerSheet;
use Modules\LMS\app\Models\Course;
use Modules\PFM\app\Http\Enums\LevyCategoriesEnum;
use Modules\PFM\app\Http\Traits\LevyTrait;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\PfmCirculars;

class LeviesSeeder extends Seeder
{
    use LevyTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levies = json_decode(file_get_contents(realpath(__DIR__ . '/LeviesSeeder.json')), true);
        $activeStatus = $this->ActiveStatus();
        foreach ($levies as $levy) {
            Levy::create([
                'name' => $levy['name'],
                'category' => $this->getLevyCategoryIdByName($levy['category']),
                'description' => $levy['description'],
                'bgt_subject_id' => $levy['bg_id'],
                'has_app' => $levy['has_app'],
                'status_id' => $activeStatus->id,
            ]);
        }
    }

    private function getLevyCategoryIdByName(string $name): ?int
    {
        foreach (LevyCategoriesEnum::cases() as $case) {
            if ($case->value === $name) {
                return $case->id();
            }
        }

        return null;
    }
}
