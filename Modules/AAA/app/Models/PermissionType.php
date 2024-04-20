<?php

namespace Modules\AAA\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AAA\Database\factories\PermissionTypeFactory;

class PermissionType extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected $table = 'permission_types';

    protected static function newFactory(): PermissionTypeFactory
    {
        //return PermissionTypeFactory::new();
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
