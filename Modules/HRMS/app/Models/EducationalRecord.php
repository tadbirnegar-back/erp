<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\FileMS\app\Models\File;
use Modules\HRMS\Database\factories\EducationalRecordFactory;
use Modules\StatusMS\app\Models\Status;

class EducationalRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'university_name',
        'field_of_study',
        'start_date',
        'end_date',
        'average',
        'work_force_id',
        'level_of_educational_id',
        'person_id',
        'status_id',
        'approver_id',
        'create_date',
        'approve_date',
    ];
    public $timestamps = false;

    protected static function newFactory(): EducationalRecordFactory
    {
        //return EducationalRecordFactory::new();
    }

    public function levelOfEducation(): BelongsTo
    {
        return $this->belongsTo(LevelOfEducation::class, 'level_of_educational_id');
    }

    public function workForce(): BelongsTo
    {
        return $this->belongsTo(WorkForce::class, 'work_force_id');
    }

    public function attachments(): MorphToMany
    {
        return $this->morphToMany(File::class,
            'attachmentable',
            'attachmentables',
            'attachmentable_id',
            'attachment_id')
            ->withPivot('title');
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
