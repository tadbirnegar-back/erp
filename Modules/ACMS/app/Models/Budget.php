<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AAA\app\Models\User;
use Modules\ACMS\Database\factories\BudgetFactory;
use Modules\FileMS\app\Models\File;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;
use Znck\Eloquent\Traits\BelongsToThrough;

class Budget extends Model
{
    use HasRelationships, HasRecursiveRelationships, BelongsToThrough;


    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'isSupplementary',
        'ounitFiscalYear_id',
        'parent_id',
        'circular_id',
        'create_date',
        'financial_manager_id',
        'ounit_head_id',
    ];

    public $timestamps = false;
    protected $table = 'bgt_budgets';


    public function financialManager()
    {
        return $this->belongsToThrough(Person::class, [User::class],
            foreignKeyLookup: [
                Person::class => 'person_id',
                User::class => 'financial_manager_id',
            ]);
    }

    public function ounitHead()
    {
        return $this->belongsToThrough(Person::class, [User::class],
            foreignKeyLookup: [
                Person::class => 'person_id',
                User::class => 'ounit_head_id',
            ]);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'bgtBudget_status', 'budget_id', 'status_id')
            ->withPivot([
                'create_date as status_create_date',
                'file_id',
                'creator_id',
                'description'
            ])
            ->using(BudgetStatus::class);
    }

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, BudgetStatus::class, 'budget_id', 'id', 'id', 'status_id')
            ->orderBy('bgtBudget_status.create_date', 'desc');
    }

    public function ounitFiscalYear(): BelongsTo
    {
        return $this->belongsTo(OunitFiscalYear::class, 'ounitFiscalYear_id');
    }

    public function fiscalYear()
    {
        return $this->hasOneThrough(FiscalYear::class, OunitFiscalYear::class, 'id', 'id', 'ounitFiscalYear_id', 'fiscal_year_id');
    }

    public function circularFile()
    {
        return $this->hasOneDeep(File::class, [
            Circular::class,
        ],
            [
                'id',
                'id'

            ],
            [
                'circular_id',
                'file_id',

            ]);
    }

    public function ounit()
    {
        return $this->hasOneThrough(OrganizationUnit::class, OunitFiscalYear::class, 'id', 'id', 'ounitFiscalYear_id', 'ounit_id');
    }

    public function village()
    {
        return $this->hasOneDeep(VillageOfc::class, [OunitFiscalYear::class,
            OrganizationUnit::class,
        ], [
            'id',
            'id',
            'id',
        ], [
            'ounitFiscalYear_id',
            'ounit_id',
            'unitable_id',
        ]);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class, 'budget_id');
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', '=', self::class);
    }
}
