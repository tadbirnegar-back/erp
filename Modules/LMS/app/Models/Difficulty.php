<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Difficulty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'difficulties';

    protected $fillable = [
        'id',
        'name'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'difficulty_id', 'id');
    }
}
