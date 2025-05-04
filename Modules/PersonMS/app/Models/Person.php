<?php

namespace Modules\PersonMS\app\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\AAA\app\Models\User as AAAUser;
use Modules\ACC\app\Models\Account;
use Modules\AddressMS\app\Models\Address;
use Modules\CustomerMS\app\Models\Customer;
use Modules\FileMS\app\Models\File;
use Modules\HRMS\app\Models\CourseRecord;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\Isar;
use Modules\HRMS\app\Models\MilitaryService;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\Relative;
use Modules\HRMS\app\Models\Resume;
use Modules\HRMS\app\Models\WorkForce;
use Modules\LMS\app\Models\Teacher;
use Modules\PersonMS\Database\factories\PersonFactory;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\LaravelAdjacencyList\Tests\IdeHelper\Models\User;

class Person extends Model
{
    use EagerLoadPivotTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'display_name',
        'national_code',
        'email',
        'phone',
        'personable_id',
        'personable_type',
        'create_date',
        'profile_picture_id',
    ];
    protected $table = 'persons';
    public $timestamps = false;

    protected static function newFactory(): PersonFactory
    {
        //return PersonFactory::new();
    }

    public function personable()
    {
        return $this->morphTo();
    }

    public function status()
    {
        return $this->belongsToMany(Status::class)->latest('create_date')->take(1);
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class)->latest('create_date');
    }

    public function avatar()
    {
        return $this->belongsTo(File::class, 'profile_picture_id');
    }

    public function workForce(): HasOne
    {
        return $this->hasOne(WorkForce::class, 'person_id');
    }

    public function teacherWorkforce(): HasOne
    {
        return $this->hasOne(WorkForce::class, 'person_id')->where('workforceable_type', Teacher::class);
    }

    public function workForces(): HasMany
    {
        return $this->hasMany(WorkForce::class, 'person_id');
    }


    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(AAAUser::class, 'person_id');
    }


    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }


    public function employee()
    {
        return $this->hasOneThrough(
            Employee::class,       // Final model: Employee
            WorkForce::class,      // Intermediate model: WorkForce
            'person_id',           // Foreign key on WorkForce model (references Person's primary key)
            'id',                  // Foreign key on Employee model (references WorkForce's primary key)
            'id',                  // Local key on Person model (primary key of Person)
            'workforceable_id'     // Local key on WorkForce model (references Employee's primary key)
        )->where('workforceable_type', Employee::class); // Ensures WorkForce entry is related to Employee
    }

    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    public function latestRecruitmentScript()
    {
        return $this->hasOneDeep(
            RecruitmentScript::class,
            [WorkForce::class, Employee::class], // Intermediate models
            [
                'person_id',                // Foreign key on the WorkForce model
                'id',         // Foreign key on the Employee model
                'employee_id'               // Foreign key on the RecruitmentScript model
            ],
            [
                'id',                       // Local key on the current model
                'workforceable_id',                       // Local key on the WorkForce model
                'id'                        // Local key on the Employee model
            ]
        )
            ->where('workforceable_type', Employee::class)
            ->latest('create_date');
    }

    public function position()
    {
        return $this->hasOneDeep(
            Position::class,
            [WorkForce::class, Employee::class, RecruitmentScript::class], // Intermediate models
            [
                'person_id',                // Foreign key on the WorkForce model
                'id',         // Foreign key on the Employee model
                'employee_id',               // Foreign key on the RecruitmentScript model
                'id',
            ],
            [
                'id',                       // Local key on the current model
                'workforceable_id',                       // Local key on the WorkForce model
                'id',
                // Local key on the Employee model
                'position_id',

            ]
        )
            ->where('workforceable_type', Employee::class)
            ->latest('create_date');
    }

    public function recruitmentScripts()
    {
        return $this->hasManyDeep(
            RecruitmentScript::class,
            [WorkForce::class, Employee::class], // Intermediate models
            [
                'person_id',                // Foreign key on the WorkForce model
                'id',         // Foreign key on the Employee model
                'employee_id'               // Foreign key on the RecruitmentScript model
            ],
            [
                'id',                       // Local key on the current model
                'workforceable_id',                       // Local key on the WorkForce model
                'id'                        // Local key on the Employee model
            ]
        )
            ->where('workforceable_type', Employee::class)
            ->latest('create_date');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'person_id');
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'entity_id');
    }

    public function isar(): HasOne
    {
        return $this->hasOne(Isar::class, 'person_id');
    }

    public function militaryService(): HasOne
    {
        return $this->hasOne(MilitaryService::class, 'person_id');
    }

    public function relatives(): HasMany
    {
        return $this->hasMany(Relative::class, 'person_id');
    }

    public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class, 'person_id');
    }

    public function educationalRecords(): HasMany
    {
        return $this->hasMany(EducationalRecord::class, 'person_id');
    }

    public function courseRecords(): HasMany
    {
        return $this->hasMany(CourseRecord::class, 'person_id');
    }

    public function natural(): BelongsTo
    {
        return $this->belongsTo(Natural::class, 'personable_id', 'id');
    }
    

}
