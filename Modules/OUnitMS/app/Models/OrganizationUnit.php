<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\AAA\app\Models\User;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\Document;
use Modules\ACC\app\Models\OunitAccImport;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\ACMS\app\Models\OunitFiscalYear;
use Modules\EMS\app\Http\Enums\EnactmentStatusEnum;
use Modules\EMS\app\Http\Enums\MeetingTypeEnum;
use Modules\EMS\app\Http\Enums\SettingsEnum;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingMember;
use Modules\EMS\app\Models\MeetingType;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\EvalMS\app\Models\Evaluator;
use Modules\Gateway\app\Models\Payment;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\app\Http\GlobalScope\ActiveScope;
use Modules\OUnitMS\Database\factories\OrganizationUnitFactory;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentEagerLimitXLaravelAdjacencyList\Eloquent\HasEagerLimitAndRecursiveRelationships;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Staudenmeir\EloquentHasManyDeep\HasTableAlias;
use Znck\Eloquent\Traits\BelongsToThrough;

class OrganizationUnit extends Model
{
    use HasFactory;
    use HasEagerLimitAndRecursiveRelationships, HasTableAlias, HasRelationships;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name',
        'unitable_id',
        'unitable_type',
        'head_id',
        'parent_id',
        'status_id'
    ];
    public $timestamps = false;

    protected static function newFactory(): OrganizationUnitFactory
    {
        //return OrganizationUnitFactory::new();
    }

    public function unitable(): MorphTo
    {
        return $this->morphTo();
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function evaluations(): BelongsToMany
    {
        return $this->belongsToMany(Evaluation::class);
    }

    public function evaluators(): HasMany
    {
        return $this->hasMany(Evaluator::class, 'organization_unit_id');
    }

    public function evaluator(): HasOne
    {
        return $this->hasOne(Evaluator::class, 'organization_unit_id');
    }

    use BelongsToThrough;

    public function person()
    {
        return $this->belongsToThrough(Person::class, User::class, foreignKeyLookup: [
            User::class => 'head_id',
            Person::class => 'person_id',
        ]);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'organization_unit_id');
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'recruitment_script_status');
    }

    public function recruitmentScripts(): HasMany
    {
        return $this->hasMany(RecruitmentScript::class, 'organization_unit_id');
    }


    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'ounit_position', 'ounit_id', 'position_id')->whereHas('status', function ($query) {
            $query->where('name', '=', 'فعال');
        })->distinct();
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'ounit_id')->where('isTemplate', '=', false);
    }

    public function firstFreeMeetingByNow(): HasOne
    {
        // Fetch the max days for reception from settings
        $maxDays = \DB::table('settings')
            ->where('key', SettingsEnum::MAX_DAY_FOR_RECEPTION->value)
            ->value('value');


        // Fetch the enactment limit per meeting from settings
        $enactmentLimitPerMeeting = \DB::table('settings')
            ->where('key', SettingsEnum::ENACTMENT_LIMIT_PER_MEETING->value)
            ->value('value');


        $meetingtypeId = \DB::table('meeting_types')
            ->where('title', MeetingTypeEnum::HEYAAT_MEETING->value)
            ->value('id');
        return $this->hasOne(Meeting::class, 'ounit_id')
            ->where('meeting_type_id', $meetingtypeId)
            ->whereBetween('meeting_date', [now(), now()->addDays($maxDays)])
            ->whereNotExists(function ($query) use ($enactmentLimitPerMeeting) {
                $query->selectRaw('1')
                    ->from('enactment_meeting')
                    ->join('enactments', 'enactments.id', '=', 'enactment_meeting.enactment_id')
                    ->whereColumn('enactment_meeting.meeting_id', 'meetings.id')
                    ->whereNotExists(function ($subQuery) {
                        $subQuery->selectRaw('1')
                            ->from('enactment_status')
                            ->join('statuses', 'statuses.id', '=', 'enactment_status.status_id')
                            ->whereColumn('enactment_status.enactment_id', 'enactments.id')
                            ->where('statuses.name', '=', EnactmentStatusEnum::CANCELED->value);
                    })
                    ->groupBy('enactment_meeting.meeting_id')
                    ->havingRaw('COUNT(DISTINCT enactment_meeting.enactment_id) >= ?', [$enactmentLimitPerMeeting]);
            })
            ->orderBy('meeting_date', 'asc');

    }

    public function firstFreeMeetingByNowForFreeZone(): HasOne
    {
        // Fetch the max days for reception from settings
        $maxDays = \DB::table('settings')
            ->where('key', SettingsEnum::MAX_DAY_FOR_RECEPTION->value)
            ->value('value');

        // Fetch the enactment limit per meeting from settings
        $enactmentLimitPerMeeting = \DB::table('settings')
            ->where('key', SettingsEnum::ENACTMENT_LIMIT_PER_MEETING->value)
            ->value('value');


        $meetingtypeId = \DB::table('meeting_types')
            ->where('title', MeetingTypeEnum::FREE_ZONE->value)
            ->value('id');

        return $this->hasOne(Meeting::class, 'ounit_id')
            ->where('meeting_type_id', $meetingtypeId)
            ->whereBetween('meeting_date', [now(), now()->addDays($maxDays)])
            ->whereNotExists(function ($query) use ($enactmentLimitPerMeeting) {
                $query->selectRaw('1')
                    ->from('enactment_meeting')
                    ->join('enactments', 'enactments.id', '=', 'enactment_meeting.enactment_id')
                    ->whereColumn('enactment_meeting.meeting_id', 'meetings.id')
                    ->whereNotExists(function ($subQuery) {
                        $subQuery->selectRaw('1')
                            ->from('enactment_status')
                            ->join('statuses', 'statuses.id', '=', 'enactment_status.status_id')
                            ->whereColumn('enactment_status.enactment_id', 'enactments.id')
                            ->where('statuses.name', '=', EnactmentStatusEnum::CANCELED->value); // Exclude enactments with "باطل شده"
                    })
                    ->groupBy('enactment_meeting.meeting_id')
                    ->havingRaw('COUNT(DISTINCT enactment_meeting.enactment_id) >= ?', [$enactmentLimitPerMeeting]);
            })
            ->orderBy('meeting_date', 'asc');

    }

    public function fullMeetingsByNow(): HasMany
    {
        // Fetch the max days for reception from settings
        $maxDays = \DB::table('settings')
            ->where('key', SettingsEnum::MAX_DAY_FOR_RECEPTION->value)
            ->value('value');

        // Fetch the enactment limit per meeting from settings
        $enactmentLimitPerMeeting = \DB::table('settings')
            ->where('key', SettingsEnum::ENACTMENT_LIMIT_PER_MEETING->value)
            ->value('value');


        $meetingtypeId = \DB::table('meeting_types')
            ->where('title', MeetingTypeEnum::HEYAAT_MEETING->value)
            ->value('id');
        // Fetch all the meetings that have reached or exceeded the enactment limit
        return $this->hasMany(Meeting::class, 'ounit_id')
            ->where('meeting_type_id', $meetingtypeId)
            ->whereBetween('meeting_date', [now(), now()->addDays($maxDays)]) // Filter by meeting date range
            ->whereExists(function ($query) use ($enactmentLimitPerMeeting) {
                $query->selectRaw('1')
                    ->from('enactment_meeting')
                    ->join('enactments', 'enactments.id', '=', 'enactment_meeting.enactment_id') // Join enactments table
                    ->whereColumn('enactment_meeting.meeting_id', 'meetings.id') // Match meeting IDs
                    ->whereNotExists(function ($subQuery) {
                        $subQuery->selectRaw('1')
                            ->from('enactment_status')
                            ->join('statuses', 'statuses.id', '=', 'enactment_status.status_id') // Join statuses table
                            ->whereColumn('enactment_status.enactment_id', 'enactments.id') // Match enactment IDs
                            ->where('statuses.name', '=', EnactmentStatusEnum::CANCELED->value); // Exclude enactments with this status
                    })
                    ->groupBy('enactment_meeting.meeting_id') // Group by meeting
                    ->havingRaw('COUNT(DISTINCT enactment_meeting.enactment_id) >= ?', [$enactmentLimitPerMeeting]); // Check enactment count
            })
            ->orderBy('meeting_date', 'asc'); // Order by meeting date

    }

    public function fullMeetingsByNowForFreeZone(): HasMany
    {
        // Fetch the max days for reception from settings
        $maxDays = \DB::table('settings')
            ->where('key', SettingsEnum::MAX_DAY_FOR_RECEPTION->value)
            ->value('value');

        // Fetch the enactment limit per meeting from settings
        $enactmentLimitPerMeeting = \DB::table('settings')
            ->where('key', SettingsEnum::ENACTMENT_LIMIT_PER_MEETING->value)
            ->value('value');


        $meetingtypeId = \DB::table('meeting_types')
            ->where('title', MeetingTypeEnum::FREE_ZONE->value)
            ->value('id');
        // Fetch all the meetings that have reached or exceeded the enactment limit
        return $this->hasMany(Meeting::class, 'ounit_id')
            ->where('meeting_type_id', $meetingtypeId)
            ->whereBetween('meeting_date', [now(), now()->addDays($maxDays)]) // Filter by meeting date range
            ->whereExists(function ($query) use ($enactmentLimitPerMeeting) {
                $query->selectRaw('1')
                    ->from('enactment_meeting')
                    ->join('enactments', 'enactments.id', '=', 'enactment_meeting.enactment_id') // Join enactments table
                    ->whereColumn('enactment_meeting.meeting_id', 'meetings.id') // Match meeting IDs
                    ->whereNotExists(function ($subQuery) {
                        $subQuery->selectRaw('1')
                            ->from('enactment_status')
                            ->join('statuses', 'statuses.id', '=', 'enactment_status.status_id') // Join statuses table
                            ->whereColumn('enactment_status.enactment_id', 'enactments.id') // Match enactment IDs
                            ->where('statuses.name', '=', EnactmentStatusEnum::CANCELED->value); // Exclude enactments with this status
                    })
                    ->groupBy('enactment_meeting.meeting_id') // Group by meeting
                    ->havingRaw('COUNT(DISTINCT enactment_meeting.enactment_id) >= ?', [$enactmentLimitPerMeeting]); // Check enactment count
            })
            ->orderBy('meeting_date', 'asc'); // Order by meeting date

    }

    public function meetingTemplate(): HasOne
    {
        return $this->hasOne(Meeting::class, 'ounit_id')
            ->where('isTemplate', '=', true);
    }

    public function cityMeetings()
    {
        $meetingType = MeetingType::where('title', MeetingTypeEnum::HEYAAT_MEETING->value)->first();

        return $this->hasManyDeep(Meeting::class, [OrganizationUnit::class],
            [
                'parent_id',
                'ounit_id',
            ],
            [
                'id',
                'id'

            ])
            ->where('isTemplate', '=', false)
            ->where('meeting_type_id', $meetingType->id);
    }


    public function villageWithFreeZone(): HasMany
    {
        return $this->hasMany(VillageOfc::class, 'free_zone_id', 'unitable_id');
    }

    public function meetingMembers()
    {
        return $this->hasManyThrough(MeetingMember::class, Meeting::class,
            'ounit_id', 'meeting_id')
            ->where('isTemplate', '=', true)
            ->with('mr', 'person.avatar');
    }

    public function meetingMembersAzad()
    {
        $mtID = MeetingType::where('title', MeetingTypeEnum::FREE_ZONE->value)->first()->id;
        return $this->hasManyThrough(MeetingMember::class, Meeting::class,
            'ounit_id', 'meeting_id')
            ->where('isTemplate', '=', true)
            ->where('meeting_type_id', $mtID)
            ->with('mr', 'person.avatar');
    }


    public function meetingMembersHeyat()
    {
        $mtID = MeetingType::where('title', MeetingTypeEnum::OLGOO->value)->first()->id;
        $mtIDFz = MeetingType::where('title', MeetingTypeEnum::FREE_ZONE->value)->first()->id;
        return $this->hasManyThrough(MeetingMember::class, Meeting::class,
            'ounit_id', 'meeting_id')
            ->where('isTemplate', '=', true)
            ->where('meeting_type_id', $mtID)
            ->whereNot('meeting_type_id', $mtIDFz)
            ->with('mr', 'person.avatar');
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', '=', self::class);
    }

    public function village(): HasOne
    {
        return $this->hasOne(VillageOfc::class, 'id', 'unitable_id');
    }

    public function fiscalYears()
    {
        return $this->belongsToMany(FiscalYear::class, 'ounit_fiscalYear', 'ounit_id', 'fiscal_year_id');
    }

    public function importedResult()
    {
        return $this->hasOne(OunitAccImport::class, 'ounit_id');
    }

    public function ounitFiscalYears(): HasMany
    {
        return $this->hasMany(OunitFiscalYear::class, 'ounit_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'ounit_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'ounit_id');
    }


    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope());
    }

}
