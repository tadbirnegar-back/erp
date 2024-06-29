<?php

namespace Modules\PersonMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\AAA\app\Models\User as AAAUser;
use Modules\AddressMS\app\Models\Address;
use Modules\FileMS\app\Models\File;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\WorkForce;
use Modules\PersonMS\Database\factories\PersonFactory;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\LaravelAdjacencyList\Tests\IdeHelper\Models\User;

class Person extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
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


}
