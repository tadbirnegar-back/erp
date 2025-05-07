<?php

namespace Modules\AAA\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\BranchMS\app\Models\Section;
use Modules\HRMS\app\Models\PositionRole;
use Modules\StatusMS\app\Models\Status;

//use Modules\AAA\Database\factories\RoleFactory;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name' , 'status_id'];
    public $timestamps = false;

    protected static function newFactory(): RoleFactory
    {
        //return RoleFactory::new();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, table: 'role_permission');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public static function GetAllStatuses()
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function permissionsWithModuleCategory()
    {
        return $this->permissions()->with(['moduleCategory']);
    }

    public function RolePosition()
    {
        return $this->hasMany(PositionRole::class, 'role_id');

    }
}
