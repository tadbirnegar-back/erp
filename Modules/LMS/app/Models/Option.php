<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\OptionFactory;

class Option extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'options';

    protected $fillable = [
        'id',
        'is_correct',
        'question_id',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

}
