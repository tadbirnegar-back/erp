<?php

namespace Modules\VCM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\VCM\Database\factories\VcmVersionsFactory;

class VcmVersions extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'vcm_versions';

    protected $fillable = [
        'create_date',
        'high_version',
        'low_version',
        'mid_version'
    ];

    public $timestamps = false;

}
