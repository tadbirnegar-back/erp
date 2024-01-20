<?php

namespace Modules\AAA\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\AAA\Database\factories\RoleFactory;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected static function newFactory(): RoleFactory
    {
        //return RoleFactory::new();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class,table: 'role_permission');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
