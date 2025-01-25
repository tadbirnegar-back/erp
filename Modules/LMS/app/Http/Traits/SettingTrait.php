<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Http\Enums\SettingEnum;
use Modules\LMS\app\Models\Difficulty;
use Modules\LMS\app\Models\QuestionType;
use Modules\SettingsMS\app\Models\Setting;

trait SettingTrait
{
    private static string $difficulty = SettingEnum::DIFFICULTY->value;
    private static string $questionType = SettingEnum::QUESTION_TYPE->value;


    public function showDropDowns()
    {
        $Q_type = QuestionType::all();
        $difficulty = Difficulty::all();
        return [
            'questionType' => $Q_type,
            'difficulty' => $difficulty
        ];
//        return Setting::where('key', $this::$difficulty, $this::$questionType)->get();

    }

    public function dataToInsert($data)
    {
        $settings = [
            ['key' => 'Difficulty_for_exam', 'value' => $data['Difficulty']],
            ['key' => 'question_type_for_exam', 'value' => $data['questionType']],
            ['key' => 'question_numbers_perExam', 'value' => $data['questionNumber']],
            ['key' => 'time_per_questions', 'value' => $data['timePerQuestion']],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        return true;
    }
}
