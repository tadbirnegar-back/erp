<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HRMS\Database\factories\LevelOfEducationFactory;

class LevelOfEducation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected $table = 'levels_of_education';

    protected static function newFactory(): LevelOfEducationFactory
    {
        //return LevelOfEducationFactory::new();
    }
}
