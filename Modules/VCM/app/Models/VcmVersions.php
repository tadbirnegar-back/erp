<?php

namespace Modules\VCM\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\EMS\app\Http\Traits\DateTrait;
use Modules\VCM\Database\factories\VcmVersionsFactory;

class VcmVersions extends Model
{
    use HasFactory , DateTrait;

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


    public function createDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                $jalali = $this->formatToShortJalali(convertDateTimeGregorianToJalaliDateTime($value));

                return $jalali;
            }
        );
    }


}
