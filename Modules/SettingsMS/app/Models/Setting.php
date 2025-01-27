<?php

namespace Modules\SettingsMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\SettingsMS\Database\factories\SettingFactory;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'key',
        'value',
        'Difficulty_for_exam',
        'question_type_for_exam',
        'question_numbers_perExam',
        'time_per_questions',
    ];

    public $timestamps = false;

    protected static function newFactory(): SettingFactory
    {
        //return SettingFactory::new();
    }
}
