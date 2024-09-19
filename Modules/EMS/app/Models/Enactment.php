<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\EnactmentReviewEnum;
use Modules\EMS\app\Http\Enums\EnactmentStatusEnum;
use Modules\EMS\app\Http\Enums\RolesEnum;
use Modules\EMS\Database\factories\EnactmentFactory;
use Modules\FileMS\app\Models\File;
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
        return $this->hasOneThrough(Status::class, EnactmentStatus::class, 'enactment_id', 'id', 'id', 'status_id')->orderBy('enactment_status.create_date', 'desc');
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
        if (!$this->relationLoaded('reviewStatuses')) {
            return null;
        }

        $reviewStatuses = $this->reviewStatuses;

        if ($reviewStatuses->count() < 3) {
            return EnactmentReview::GetAllStatuses()->firstWhere('name', EnactmentReviewEnum::UNKNOWN->value);
        }

        $result = $reviewStatuses->groupBy('id')
            ->map(fn($statusGroup) => [
                'status' => $statusGroup->first(),
                'count' => $statusGroup->count()
            ])
            ->sortByDesc('count')
            ->values();

        return $result[0]['status'];
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

    use HasRelationships;

    public function relatedDates()
    {
        return $this->hasManyDeep(Meeting::class,
            [
                Meeting::class . ' as alias',
                OrganizationUnit::class
            ],
            [
                'id',
                'id',
                'ounit_id',
            ],
            [
                'meeting_id',
                'ounit_id',
                'id',
            ]);
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
        return $this->hasManyThrough(MeetingMember::class, Meeting::class, 'id', 'meeting_id', 'meeting_id', 'id')->with('mr');
    }

    public function consultingMembers()
    {
        return $this->members()->whereHas('roles', function ($query) {
            $query->where('name', RolesEnum::KARSHENAS_MASHVARATI->value);
        })->with('person.avatar');
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


}
