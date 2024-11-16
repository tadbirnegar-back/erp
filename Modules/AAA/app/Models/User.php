<?php

namespace Modules\AAA\app\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\AddressMS\app\Models\Address;
use Modules\EMS\app\Models\MeetingMember;
use Modules\EMS\app\Models\MR;
use Modules\EvalMS\app\Models\Evaluator;
use Modules\FileMS\app\Models\File;
use Modules\Gateway\app\Models\Payment;
use Modules\HRMS\app\Http\Enums\ScriptTypeOriginEnum;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\WorkForce;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Modules\WidgetsMS\app\Models\Widget;
use Staudenmeir\EloquentHasManyDeep\Eloquent\Relations\Traits\HasEagerLimit;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasEagerLimit;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'person_id',
        'mobile',
        'password',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',

    ];

    protected array $loadedPermissionSlugs;

    public static function GetAllStatuses(): Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
//    protected $casts = [
////        'email_verified_at' => 'datetime',
////        'password' => 'hashed',
//    ];
    public function findForPassport($username)
    {
        return $this->where('mobile', $username)->first();
    }


    public function getAuthIdentifierName()
    {
        return 'mobile';
    }

//    public function permissions()
//    {
//        return $this->roles->flatMap->permissions->moduleCategory;
////        return $this->HasManyThrough(Permission::class,Role::class);
////        return $this->roles->flatMap(function ($role) {
////            return $role->permissions;
////        });
//    }
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, table: 'user_role')
            ->distinct();
    }


    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'user_status');
    }

    public function status()
    {
        return $this->belongsToMany(Status::class, 'user_status')->latest('create_date')->take(1);
    }

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, UserStatus::class, 'user_id', 'id', 'id', 'status_id')
            ->orderBy('user_status.id', 'desc');
    }


    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function hasPermissionForRoute($route)
    {
        // Assuming a relationship with a permissions table
        return !($this->permissions()->where('slug', '=', $route)->first() == null);
    }

    public function permissions()
    {
        return $this->hasManyDeep(Permission::class, ['user_role', Role::class, 'role_permission'])
            ->distinct();
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class, 'creator_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'creator_id');
    }

    public function widgets()
    {
        return $this->permissions()
            ->whereHas('permissionTypes', function ($query) {
                $query->where('name', 'widget');
            })
            ->with(['widgets' => function ($query) {
                $query->where('user_id', $this->id);
            }]);

    }

    public function activeWidgets(): HasMany
    {
        return $this->hasMany(Widget::class)
            ->where('isActivated', true)->with('permission');
    }

    public function evaluators()
    {
        return $this->hasMany(Evaluator::class);
    }

    public function evaluator()
    {
        return $this->hasOne(Evaluator::class)->latest('id');
    }

    public function organizationUnits(): HasMany
    {
        return $this->hasMany(OrganizationUnit::class, 'head_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'user_id');
    }

    public function employee()
    {
        return $this->hasOneDeep(
            Employee::class,
            [Person::class, Workforce::class],
            [
                'id', // Foreign key on the persons table...
                'person_id', // Foreign key on the workforces table...
                'id', // Foreign key on the employees table...
            ],
            [
                'person_id', // Local key on the users table...
                'id', // Local key on the persons table...
                'workforceable_id', // Local key on the workforces table...
            ]
        );
    }


    use HasRelationships;

    public function activeRecruitmentScripts()
    {
        return $this->recruitmentScripts()
//            ->whereHas('latestStatus', function ($query) {
//            $query
            ->join('recruitment_script_status as rss', 'recruitment_scripts.id', '=', 'rss.recruitment_script_id')
            ->join('statuses as s', 'rss.status_id', '=', 's.id')
            ->where('s.name', 'فعال')
            ->where('rss.create_date', function ($subQuery) {
                $subQuery->selectRaw('MAX(create_date)')
                    ->from('recruitment_script_status as sub_rss')
                    ->whereColumn('sub_rss.recruitment_script_id', 'rss.recruitment_script_id');
            });
//        });
    }

    public function activeRecruitmentScript()
    {
        return $this->activeRecruitmentScripts()->whereHas('scriptType', function ($query) {
            $query->where('origin_id', ScriptTypeOriginEnum::Main->value);
        });
    }

    public function activeDistrictRecruitmentScript()
    {
        return $this->activeRecruitmentScripts()
            ->join('organization_units', 'recruitment_scripts.organization_unit_id', '=', 'organization_units.id')
            ->where('organization_units.unitable_type', DistrictOfc::class)
            ->orderBy('recruitment_scripts.start_date', 'desc')//            ->select('recruitment_scripts.*')
            ; // Add the necessary columns here

//        return $this->activeRecruitmentScripts()->whereHas('ounit', function ($query) {
//            $query->where('organization_units.unitable_type', DistrictOfc::class);
//        })
//            ->orderBy('recruitment_scripts.start_date', 'desc');
    }

    public function recruitmentScripts()
    {
        return $this->hasManyDeep(
            RecruitmentScript::class,
            [Person::class, Workforce::class, Employee::class],
            [
                'id', // Foreign key on the persons table...
                'person_id', // Foreign key on the workforces table...
                'id', // Foreign key on the employees table...
                'employee_id' // Foreign key on the recruitment_scripts table...
            ],
            [
                'person_id', // Local key on the users table...
                'id', // Local key on the persons table...
                'workforceable_id', // Local key on the workforces table...
                'id' // Local key on the employees table...
            ]
        );
    }

    public function latestRecruitmentScript()
    {
        return $this->recruitmentScripts()->latest('create_date')->take(1);
    }

    public function mr()
    {
        return $this->hasOneThrough(MR::class, MeetingMember::class, 'employee_id', 'id', 'id', 'mr_id')->orderBy('meeting_members.id', 'desc');
    }

    public function loadPermissions()
    {
        $this->loadedPermissions = $this->permissions()->pluck('slug')->toArray();
    }


    public function hasPermission($slug): bool
    {
        if (is_null($this->loadedPermissions)) {
            $this->loadPermissions(); // Load permissions if not already loaded
        }

        return in_array($slug, $this->loadedPermissions);
    }

    public function hasAllPermissions(array $slugs): bool
    {
        if (is_null($this->loadedPermissions)) {
            $this->loadPermissions();
        }

        // Check if all required permissions exist in the loaded permissions
        return empty(array_diff($slugs, $this->loadedPermissions));
    }
}
