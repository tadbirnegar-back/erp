<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\ACMS\Database\factories\BudgetFactory;
use Modules\StatusMS\app\Models\Status;

class Budget extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'isSupplementary',
        'ounitFiscalYear_id',
        'parent_id',
    ];

    public $timestamps = false;
    protected $table = 'bgt_budgets';

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'bgtBudget_status', 'budget_id', 'status_id');
    }

    public function ounitFiscalYear(): BelongsTo
    {
        return $this->belongsTo(OunitFiscalYear::class, 'ounitFiscalYear_id');
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', '=', self::class);
    }
}
