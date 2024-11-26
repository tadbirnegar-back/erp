<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\MeetingTypeEnum;
use Modules\EMS\app\Http\Enums\SettingsEnum;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingMember;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\EvalMS\app\Models\Evaluator;
use Modules\Gateway\app\Models\Payment;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\OUnitMS\Database\factories\OrganizationUnitFactory;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentEagerLimitXLaravelAdjacencyList\Eloquent\HasEagerLimitAndRecursiveRelationships;
use Staudenmeir\EloquentHasManyDeep\HasTableAlias;
use Znck\Eloquent\Traits\BelongsToThrough;

class OrganizationUnit extends Model
{
    use HasFactory;
    use HasEagerLimitAndRecursiveRelationships, HasTableAlias;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name',
        'unitable_id',
        'unitable_type',
        'head_id',
        'parent_id',];
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
        });
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
            ->whereBetween('meeting_date', [now(), now()->addDays($maxDays)]) // Filter by meeting date range
            ->whereNotExists(function ($query) use ($enactmentLimitPerMeeting) {
                $query->selectRaw('1')
                    ->from('enactment_meeting')
                    ->whereColumn('enactment_meeting.meeting_id', 'meetings.id') // Match meeting IDs
                    ->groupBy('enactment_meeting.meeting_id')                  // Group by meeting
                    ->havingRaw('COUNT(DISTINCT enactment_id) >= ?', [$enactmentLimitPerMeeting]); // Check enactment count
            })
            ->orderBy('meeting_date', 'asc'); // Get the nearest meeting
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
                    ->whereColumn('enactment_meeting.meeting_id', 'meetings.id') // Match meeting IDs
                    ->groupBy('enactment_meeting.meeting_id')                  // Group by meeting
                    ->havingRaw('COUNT(DISTINCT enactment_id) >= ?', [$enactmentLimitPerMeeting]); // Check enactment count
            })
            ->orderBy('meeting_date', 'asc'); // Order by meeting date
    }

    public function meetingTemplate(): HasOne
    {
        return $this->hasOne(Meeting::class, 'ounit_id')
            ->where('isTemplate', '=', true);
    }

    public function meetingMembers()
    {
        return $this->hasManyThrough(MeetingMember::class, Meeting::class,
            'ounit_id', 'meeting_id')
            ->where('isTemplate', '=', true)
            ->with('mr', 'person.avatar');
    }

    public static function GetAllStatuses(): Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

}
