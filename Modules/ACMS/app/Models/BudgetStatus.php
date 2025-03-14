<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\AAA\app\Models\User;
use Modules\ACMS\Database\factories\BudgetStatusFactory;
use Modules\FileMS\app\Models\File;
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
        'file_id',
        'description',
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

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
