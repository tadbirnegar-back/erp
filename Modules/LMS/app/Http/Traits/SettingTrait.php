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

    public function showDropDownsComprehensive()
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
            ['key' => 'pass_score', 'value' => $data['passScore']],

        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        return true;
    }

    public function dataToInsertComprehensive($data)
    {
        $settings = [
            ['key' => 'Difficulty_for_exam_comprehensive', 'value' => $data['Difficulty']],
            ['key' => 'question_type_for_exam_comprehensive', 'value' => $data['questionType']],
            ['key' => 'question_numbers_perExam_comprehensive', 'value' => $data['questionNumber']],
            ['key' => 'time_per_questions_comprehensive', 'value' => $data['timePerQuestion']],
            ['key' => 'pass_score_comprehensive', 'value' => $data['passScore']],

        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        return true;
    }


    public function LastSettingShow()
    {
        $settings = Setting::select(['key', 'value'])
            ->whereIn('key', [
                'pass_score',
                'question_numbers_perExam',
                'time_per_questions',
                'Difficulty_for_exam',
                'question_type_for_exam'
            ])
            ->get()
            ->keyBy('key');;

        $Q_type = QuestionType::all();
        $difficulty = Difficulty::all();

        $questionTypeForExam = $settings->where('key', 'question_type_for_exam')->first();
        $difficultyForExam = $settings->where('key', 'Difficulty_for_exam')->first();

        $questionTypeForExamValue = optional($questionTypeForExam)->value;
        $difficultyForExamValue = optional($difficultyForExam)->value;

        $questionTypeName = $Q_type->where('id', $questionTypeForExamValue)->pluck('name')->first();
        $difficultyName = $difficulty->where('id', $difficultyForExamValue)->pluck('name')->first();

        return [
            'questionType' => [
                'id' => $questionTypeForExamValue,
                'name' => $questionTypeName,
            ],
            'questionDifficulty' => [
                'id' => $difficultyForExamValue,
                'name' => $difficultyName,
            ],
            'pass_score' => optional($settings->get('pass_score'))->value,
            'question_numbers_perExam' => optional($settings->get('question_numbers_perExam'))->value,
            'time_per_questions' => optional($settings->get('time_per_questions'))->value,
        ];
    }

    public function LastSettingShowComprehensive()
    {
        $settings = Setting::select(['key', 'value'])
            ->whereIn('key', [
                'pass_score_comprehensive',
                'question_numbers_perExam_comprehensive',
                'time_per_questions_comprehensive',
                'Difficulty_for_exam_comprehensive',
                'question_type_for_exam_comprehensive'
            ])
            ->get()
            ->keyBy('key');;

        $Q_type = QuestionType::all();
        $difficulty = Difficulty::all();

        $questionTypeForExam = $settings->where('key', 'question_type_for_exam_comprehensive')->first();
        $difficultyForExam = $settings->where('key', 'Difficulty_for_exam_comprehensive')->first();

        $questionTypeForExamValue = optional($questionTypeForExam)->value;
        $difficultyForExamValue = optional($difficultyForExam)->value;

        $questionTypeName = $Q_type->where('id', $questionTypeForExamValue)->pluck('name')->first();
        $difficultyName = $difficulty->where('id', $difficultyForExamValue)->pluck('name')->first();

        return [
            'questionType' => [
                'id' => $questionTypeForExamValue,
                'name' => $questionTypeName,
            ],
            'questionDifficulty' => [
                'id' => $difficultyForExamValue,
                'name' => $difficultyName,
            ],
            'pass_score' => optional($settings->get('pass_score_comprehensive'))->value,
            'question_numbers_perExam' => optional($settings->get('question_numbers_perExam_comprehensive'))->value,
            'time_per_questions' => optional($settings->get('time_per_questions_comprehensive'))->value,
        ];
    }


}
