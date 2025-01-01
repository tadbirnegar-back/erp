<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\ACMS\Database\factories\OunitFiscalYearFactory;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;

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

    public function budget(): HasOne
    {
        return $this->hasOne(Budget::class, 'ounitFiscalYear_id');
    }

    public function village()
    {
        return $this->hasOneThrough(VillageOfc::class, OrganizationUnit::class, 'id', 'id', 'ounit_id', 'unitable_id');
    }

}
