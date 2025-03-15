<?php

namespace Modules\AAA\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AAA\Database\factories\PermissionFactory;
use Modules\WidgetsMS\app\Models\Widget;
use Znck\Eloquent\Traits\BelongsToThrough;

class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): PermissionFactory
    {
        //return PermissionFactory::new();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissionTypes()
    {
        return $this->belongsTo(PermissionType::class, 'permission_type_id');
    }

    use BelongsToThrough;

    public function moduleCategory()
    {
        return $this->belongsToThrough(ModuleCategory::class, Module::class);
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(Widget::class);
    }

    public function modules()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

}
