<?php

namespace Modules\AAA\app\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;

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
        'person_id',

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

    public function permissions()
    {
        return $this->roles->flatMap->permissions;
//        return $this->HasManyThrough(Permission::class,Role::class);
//        return $this->roles->flatMap(function ($role) {
//            return $role->permissions;
//        });
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function hasPermissionForRoute($route)
    {
        // Assuming a relationship with a permissions table
        return $this->permissions()->where('slug', $route)->pluck('slug');
    }

    public static function GetAllStatuses(): Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
