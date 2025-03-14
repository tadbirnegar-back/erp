<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACMS\Database\factories\FiscalYearFactory;

class FiscalYear extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'start_date',
        'finish_date',
    ];

    public $timestamps = false;
    protected $table = 'fiscal_years';

    public function startDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                // Convert to Jalali
                $dateTimeString = convertGregorianToJalali($value);

                return $dateTimeString;
            }
        );
    }

    public function finishDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                // Convert to Jalali
                $dateTimeString = convertGregorianToJalali($value);

                return $dateTimeString;
            }
        );
    }
}
