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
use Modules\EvalMS\app\Models\Evaluator;
use Modules\FileMS\app\Models\File;
use Modules\Gateway\app\Models\Payment;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Modules\WidgetsMS\app\Models\Widget;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function findForPassport($username)
    {
        return $this->where('mobile', $username)->first();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
//        'person_id',
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
//    protected $casts = [
////        'email_verified_at' => 'datetime',
////        'password' => 'hashed',
//    ];

    public function getAuthIdentifierName()
    {
        return 'mobile';
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, table: 'user_role');
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

    public function permissions()
    {
        return $this->hasManyDeep(Permission::class, ['user_role', Role::class, 'role_permission'])
            ->distinct();
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class,'user_status');
    }    public function status()
    {
        return $this->belongsToMany(Status::class,'user_status')->latest('create_date')->take(1);
    }


    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function hasPermissionForRoute($route)
    {
        // Assuming a relationship with a permissions table
        return !($this->permissions()->where('slug','=', $route)->first() == null);
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class,'creator_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class,'creator_id');
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

    public static function GetAllStatuses(): Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
