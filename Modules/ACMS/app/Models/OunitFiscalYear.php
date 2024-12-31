<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ACMS\Database\factories\OunitFiscalYearFactory;

class OunitFiscalYear extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'ounit_id',
        'fiscal_year_id',
        'creator_id',
        'closer_id',
        'create_date',
        'close_date',
    ];

    public $timestamps = false;
    protected $table = 'ounit_fiscalYear';

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'ounitFiscalYear_id');
    }

    public function budget(): HasMany
    {
        return $this->hasMany(Budget::class, 'ounitFiscalYear_id');
    }
}
