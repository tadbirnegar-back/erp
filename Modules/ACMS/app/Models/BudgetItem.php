<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ACMS\Database\factories\BudgetItemFactory;

class BudgetItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'proposed_amount',
        'finalized_amount',
        'budget_id',
        'circular_item_id',
        'percentage',
    ];

    public $timestamps = false;
    protected $table = 'bgt_budget_items';

    public function circularItem(): BelongsTo
    {
        return $this->belongsTo(CircularItem::class, 'circular_item_id');
    }
}
