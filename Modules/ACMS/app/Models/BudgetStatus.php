<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\AAA\app\Models\User;
use Modules\ACMS\Database\factories\BudgetStatusFactory;
use Modules\PersonMS\app\Models\Person;
use Znck\Eloquent\Traits\BelongsToThrough;

class BudgetStatus extends Pivot
{
    use BelongsToThrough;

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

    public function person()
    {
        return $this->belongsToThrough(Person::class, User::class, foreignKeyLookup: [
            User::class => 'creator_id',
            Person::class => 'person_id',
        ]);
    }
}
