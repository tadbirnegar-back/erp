<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HRMS\Database\factories\RelativeFactory;

class Relative extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): RelativeFactory
    {
        //return RelativeFactory::new();
    }

    public function relativeType(): BelongsTo
    {
        return $this->belongsTo(RelativeType::class);
    }

    public function workForce(): BelongsTo
    {
        return $this->belongsTo(WorkForce::class);
    }

    public function levelOfEducation(): BelongsTo
    {
        return $this->belongsTo(LevelOfEducation::class,'level_of_educational_id');
    }
}
