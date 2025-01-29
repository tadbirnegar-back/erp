<?php

namespace Modules\VCM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\VCM\Database\factories\VcmUserVersionFactory;

class VcmUserVersion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'user_id',
        'vcm_version_id',
    ];

    public $timestamps = false;

}
