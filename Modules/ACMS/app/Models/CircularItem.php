<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\ACMS\Database\factories\CircularItemFactory;

class CircularItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'circular_id',
        'subject_id',
        'percentage',
    ];

    public $timestamps = false;
    protected $table = 'bgt_circular_items';

    public function subject(): BelongsTo
    {
        return $this->belongsTo(CircularSubject::class);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class, 'circular_item_id');
    }

    public function budgetItem(): HasOne
    {
        return $this->hasOne(BudgetItem::class, 'circular_item_id');
    }
}
