<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACMS\Database\factories\BudgetStatusFactory;

class BudgetStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'budget_id',
        'status_id',
        'creator_id',
        'create_date',
    ];

    public $timestamps = false;
    protected $table = 'bgtBudget_status';
}
