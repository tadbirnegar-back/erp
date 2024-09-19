<?php

namespace Modules\AAA\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\AAA\Database\factories\UserRoleFactory;

class UserRole extends Pivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'user_role';
    //protected $fillable = [];

    public $timestamps = false;

}
