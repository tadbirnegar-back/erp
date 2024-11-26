<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\EnactmentReviewEnum;
use Modules\EMS\app\Http\Enums\EnactmentStatusEnum;
use Modules\EMS\app\Http\Enums\MeetingTypeEnum;
use Modules\EMS\app\Http\Enums\RolesEnum;
use Modules\EMS\Database\factories\EnactmentFactory;
use Modules\FileMS\app\Models\File;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Morilog\Jalali\CalendarUtils;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Staudenmeir\EloquentHasManyDeep\HasTableAlias;
use Znck\Eloquent\Traits\BelongsToThrough;

class Enactment extends Model
{
    use HasTableAlias;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'custom_title',
        'description',
        'rejection_reason',
        'auto_serial',
        'serial',
        'title_id',
        'creator_id',
        'meeting_id',
        'rejection_file_id',
        'create_date',
        'receipt_date',
        'final_status_id',
    ];

    protected $appends = ['upshot'];

    public $timestamps = false;


    public function attachments(): MorphToMany
    {
        return $this->morphToMany(File::class,
            'attachmentable',
            'attachmentables',
            'attachmentable_id',
            'attachment_id')
            ->withPivot('title');
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'enactment_status', 'enactment_id', 'status_id')->using(EnactmentStatus::class)->withPivot(['operator_id', 'id', 'status_id']);
    }

    public function status()
    {
        return $this->hasOneThrough(Status::class, EnactmentStatus::class, 'enactment_id', 'id', 'id', 'status_id')->orderBy('enactment_status.id', 'desc');
    }


    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function enactmentReviews(): HasMany
    {
        return $this->hasMany(EnactmentReview::class, 'enactment_id');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(EnactmentTitle::class);
    }

    public function reviewStatuses()
    {
        return $this->hasManyThrough(
            Status::class,
            EnactmentReview::class,
            'enactment_id',
            'id',
            'id',
            'status_id'
        );
    }

    public function reviewStatus()
    {
        return $this->hasManyThrough(
            Status::class,
            EnactmentReview::class,
            'enactment_id',   // Foreign key on EnactmentReview for Enactment
            'id',              // Foreign key on Status for EnactmentReview
            'id',              // Local key on Enactment for EnactmentReview
            'status_id'        // Local key on EnactmentReview for Status
        )
            ->orderBy('create_date', 'desc')  // Assuming 'create_date' is the timestamp column
            ->limit(1);  // Limit to the latest one
    }

    public function userHasReviews()
    {
        return $this->hasManyThrough(
            Status::class,
            EnactmentReview::class,
            'enactment_id',
            'id',
            'id',
            'status_id'
        );
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function getUpshotAttribute()
    {
        if ($this->final_status_id) {
            return $this->finalStatus;
        }
        return EnactmentReview::GetAllStatuses()->firstWhere('name', EnactmentReviewEnum::UNKNOWN->value);
//        if (!$this->relationLoaded('reviewStatuses')) {
//            return null;
//        }

//        $reviewStatuses = $this->enactmentReviews()
//            ->whereHas('user.roles', function ($query) {
//                $query->where('name', RolesEnum::OZV_HEYAAT->value);
//            })->with('status')->get();
//
//        if ($reviewStatuses->count() < 2) {
//            return EnactmentReview::GetAllStatuses()->firstWhere('name', EnactmentReviewEnum::UNKNOWN->value);
//        }
//
//        $result = $reviewStatuses->groupBy('status.id')
//            ->map(fn($statusGroup) => [
//                'status' => $statusGroup->first(),
//                'count' => $statusGroup->count()
//            ])
//            ->sortByDesc('count')
//            ->values();
//
//        return $result[0]['status']->status;
    }

    use BelongsToThrough;

    public function creator()
    {
        return $this->belongsToThrough(Person::class, User::class, foreignKeyLookup: [
            User::class => 'creator_id',
        ]);
    }

    public function createDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $jalali = CalendarUtils::strftime('Y/m/d', strtotime($value)); // 1395-02-19
                $jalaliPersianNumbers = CalendarUtils::convertNumbers($jalali); // ۱۳۹۵-۰۲-۱۹

                return $jalaliPersianNumbers;
            },

        );
    }

    public function ounit()
    {
        return $this->belongsToThrough(OrganizationUnit::class, Meeting::class, foreignKeyLookup: [Meeting::class => 'meeting_id', OrganizationUnit::class => 'ounit_id']);
    }

    public function ounitDistrictOfc()
    {
        return $this->hasOneDeep(OrganizationUnit::class,
            [
                Meeting::class,
                OrganizationUnit::class . ' as village',
                OrganizationUnit::class . ' as townOfc',

            ], [
                'id',
                'id',
                'id',
                'id',

            ], [
                'meeting_id',
                'ounit_id',
                'parent_id',
                'parent_id',
            ]);
    }

    use HasRelationships;

    public function relatedDates()
    {

        return $this->ounitDistrictOfc();
//            ->with(['ancestorsAndSelf' => function ($query) {
//            $query->where('unitable_type', DistrictOfc::class);
//        }]);
//        return $this->hasManyDeep(Meeting::class,
//            [
//                Meeting::class . ' as alias',
//                OrganizationUnit::class
//            ],
//            [
//                'id',
//                'id',
//                'ounit_id',
//            ],
//            [
//                'meeting_id',
//                'ounit_id',
//                'id',
//            ]);
    }


    public function districtOfc()
    {
        return $this->ounit()
            ->with(['ancestorsAndSelf' => function ($query) {
                $query->where('unitable_type', DistrictOfc::class)
                    ->with('meetingMembers');
            }]);
    }

    public function meetingMembers()
    {
        return $this->hasManyDeep(MeetingMember::class,
            [
                EnactmentReview::class
            ],
            [
                'enactment_id',
                'employee_id',
            ],
            [
                'id',
                'user_id',
            ]
        );
    }


    public function members()
    {
        $meetingType = \DB::table('meeting_types')
            ->select('id')
            ->where('title', MeetingTypeEnum::HEYAAT_MEETING)
            ->first();

        return $this->hasManyDeep(MeetingMember::class, [
            EnactmentMeeting::class,
            Meeting::class,
        ],
            [
                'enactment_id',
                'id',
                'meeting_id',
            ],
            [
                'id',
                'meeting_id',
                'id',
            ])
            ->where('meetings.meeting_type_id', $meetingType->id) // Use the `id` property
            ->orderBy('enactment_meeting.create_date', 'desc')
            ->with('mr');
    }

    public function membersNew()
    {
        $meetingType = \DB::table('meeting_types')
            ->select('id')
            ->where('title', MeetingTypeEnum::HEYAAT_MEETING)
            ->first();
        return $this->hasManyDeep(MeetingMember::class, [
            EnactmentMeeting::class,
            Meeting::class,

        ],
            [
                'enactment_id',
                'id',
                'meeting_id',
            ],
            [
                'id',
                'meeting_id',
                'id',
            ])
            ->where('meetings.meeting_type_id', $meetingType->id) // Use the `id` property
            ->orderBy('enactment_meeting.create_date', 'desc');
//        return $this->hasManyThrough(MeetingMember::class, Meeting::class, 'id', 'meeting_id', 'meeting_id', 'id')->with('mr');
    }

    public function consultingMembers()
    {
        return $this->members()->whereHas('roles', function ($query) {
            $query->where('name', RolesEnum::KARSHENAS_MASHVARATI->value);
        })->with('person.avatar');
//        return $this->members()->whereHas('roles', function ($query) {
//            $query->where('name', RolesEnum::KARSHENAS_MASHVARATI->value);
//        })->with('person.avatar');
    }

    public function boardMembers()
    {
        return $this->members()->whereHas('roles', function ($query) {
            $query->where('name', RolesEnum::OZV_HEYAAT->value);
        })->with('person.avatar');
    }

    public function enactmentReviewsByMeetingMembers()
    {
        return $this->hasManyDeep(EnactmentReview::class,
            [
                EnactmentReview::class . ' as er2',
                MeetingMember::class,
                Enactment::class . ' as e2',
            ],
            [
                'enactment_id',
                'employee_id',
                'meeting_id',
                'enactment_id',

            ],
            [
                'id',
                'user_id',
                'meeting_id',
                'id',
            ]
        );
    }

    public function canceledStatus()
    {
        return $this->hasOne(EnactmentStatus::class, 'enactment_id')->whereHas('status', function ($query) {
            $query->where(function ($query) {
                $query->where('name', '=', EnactmentStatusEnum::CANCELED->value)
                    ->orWhere('name', '=', EnactmentStatusEnum::DECLINED->value);
            })->orderBy('create_date', 'desc');
        })->with('attachment');
    }

    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class, 'enactment_meeting', 'enactment_id', 'meeting_id');
    }

    public function latestMeeting(): HasOneThrough
    {
        return $this->hasOneThrough(Meeting::class, EnactmentMeeting::class, 'enactment_id', 'id', 'id', 'meeting_id')->orderBy('enactment_meeting.id', 'desc');
    }

    public function latestHeyaatMeeting(): HasOneThrough
    {
        $meetingtypeId = \DB::table('meeting_types')
            ->where('title', MeetingTypeEnum::HEYAAT_MEETING->value)
            ->value('id');

        return $this->latestMeeting()
            ->where('meeting_type_id', $meetingtypeId);
    }

    public function finalStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'final_status_id');
    }

}
