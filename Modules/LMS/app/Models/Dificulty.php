<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\DificultyFactory;

class Dificulty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'dificulties';

    protected $fillable = [
        'id',
        'name'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'difficulty_id', 'id');
    }
}
