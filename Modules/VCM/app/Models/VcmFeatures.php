<?php

namespace Modules\VCM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\AAA\app\Models\Module;
use Modules\VCM\Database\factories\VcmFeaturesFactory;

class VcmFeatures extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'vcm_features';

    protected $fillable = [
        'description',
        'vcm_version_id',
        'module_id',
        'id'
    ];

    public $timestamps = false;


    public function module()
    {
        return $this->hasOne(Module::class, 'id', 'module_id');
    }

    public function version()
    {
        return $this->belongsTo(VcmVersions::class, 'vcm_version_id', 'id');
    }

}
