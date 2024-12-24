<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\EMS\app\Http\Traits\DateTrait;
use Modules\EMS\app\Observers\MeetingDateObserver;
use Modules\EMS\app\Scopes\ActiveMeetingScope;
use Modules\EMS\Database\factories\MeetingFactory;
use Modules\HRMS\app\Models\Employee;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Staudenmeir\EloquentHasManyDeep\HasTableAlias;

class Meeting extends Model
{
    use HasTableAlias, DateTrait;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'title',
        'meeting_detail',
        'meeting_number',
        'isTemplate',
        'summary',
        'creator_id',
        'meeting_type_id',
        'ounit_id',
        'parent_id',
        'create_date',
        'start_time',
        'end_time',
        'invitation_date',
        'meeting_date',
        'reminder_date',
    ];

    protected $appends = [
        'humanReadableJalaliDate'
    ];

    public $timestamps = false;

    protected static function booted()
    {
        // using seperate scope class
        static::addGlobalScope(new ActiveMeetingScope());
    }


    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'meeting_status', 'meeting_id', 'status_id');
    }


//    protected static function boot()
//    {
//        parent::boot();
//
//        // Register the observer
//        static::observe(\Modules\EMS\app\Observers\MeetingObserver::class);
//    }


    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }


    public function getHumanReadableJalaliDateAttribute()
    {
        return $this->DateformatToHumanReadbleJalali($this->meeting_date);
    }

    public function meetingDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                $jalali = convertDateTimeGregorianToJalaliDateTime($value);

                return $jalali;
            },

            set: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                // Convert to Gregorian
                $dateTimeString = convertDateTimeHaveDashJalaliPersianCharactersToGregorian($value);

                return $dateTimeString;
            }
        );
    }

    use HasRelationships;

//    public function persons()
//    {
//        return $this->hasManyDeep(
//            Person::class,
//            [MeetingMember::class, Employee::class, WorkForce::class],
//            [
//                'meeting_id', // Foreign key on the meeting_members table...
//                'id', // Foreign key on the employees table...
//                'workforceable_id', // Foreign key on the workforces table...
//                'id' // Foreign key on the workforces table...
//            ],
//            [
//                'id', // Local key on the meetings table...
//                'employee_id', // Local key on the meeting_members table...
//                'id', // Local key on the employees table...
//                'person_id' // Local key on the workforces table...
//            ]
//        )
////            ->using(MeetingMember::class)
//            ->withPivot('meeting_members', ['mr_id'], MeetingMember::class, 'pivot');
//    }

    public function persons()
    {
        return $this->belongsToMany(Employee::class, 'meeting_members', 'meeting_id', 'employee_id')
            ->with('person')
            ->using(MeetingMember::class)
            ->withPivot('mr_id');
    }


    public function meetingMembers(): HasMany
    {
        return $this->hasMany(MeetingMember::class, 'meeting_id');
    }

    public function ounit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function enactments()
    {
        return $this->belongsToMany(Enactment::class, 'enactment_meeting', 'meeting_id', 'enactment_id');
    }

    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class, 'meeting_type_id');
    }

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, MeetingStatus::class, 'meeting_id', 'id', 'id', 'status_id')->orderBy('meeting_status.id', 'desc');
    }


}
