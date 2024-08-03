<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HRMS\Database\factories\SkillWorkForceFactory;

class SkillWorkForce extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'percentage',
    'skill_id',
    'work_force_id',];

    public $timestamps = false;
    protected $table = 'skill_work_force';

}
