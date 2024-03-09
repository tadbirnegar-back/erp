<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HRMS\Database\factories\ResumesFactory;

class Resume extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): ResumesFactory
    {
        //return ResumesFactory::new();
    }

    public function workForce(): BelongsTo
    {
        return $this->belongsTo(WorkForce::class);
    }
}
