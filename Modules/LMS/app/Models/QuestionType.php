<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\QuestionTypeFactory;

class QuestionType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'question_types';

    protected $fillable = [
        'name',
        'id'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'question_type_id', 'id');
    }
}
