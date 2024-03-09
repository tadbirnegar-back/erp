<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HRMS\Database\factories\EducationalRecordFactory;

class EducationalRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): EducationalRecordFactory
    {
        //return EducationalRecordFactory::new();
    }

    public function levelOfEducation(): BelongsTo
    {
        return $this->belongsTo(LevelOfEducation::class,'level_of_educational_id');
    }

    public function workForce(): BelongsTo
    {
        return $this->belongsTo(WorkForce::class,'work_force_id');
    }
}
