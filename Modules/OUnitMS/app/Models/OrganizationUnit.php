<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\AAA\app\Models\User;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\EvalMS\app\Models\Evaluator;
use Modules\Gateway\app\Models\Payment;
use Modules\OUnitMS\Database\factories\OrganizationUnitFactory;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;
use Znck\Eloquent\Traits\BelongsToThrough;

class OrganizationUnit extends Model
{
    use HasFactory;
    use HasRecursiveRelationships;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name',
    'unitable_id',
    'unitable_type',
    'head_id',
    'parent_id',];
    public $timestamps = false;
    protected static function newFactory(): OrganizationUnitFactory
    {
        //return OrganizationUnitFactory::new();
    }

    public function unitable(): MorphTo
    {
        return $this->morphTo();
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluations(): BelongsToMany
    {
        return $this->belongsToMany(Evaluation::class);
    }

    public function evaluator(): HasOne
    {
        return $this->hasOne(Evaluator::class, 'organization_unit_id');
    }

    public function evaluators(): HasMany
    {
        return $this->hasMany(Evaluator::class, 'organization_unit_id');
    }

    use BelongsToThrough;

    public function person()
    {
        return $this->belongsToThrough(Person::class, User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'organization_unit_id');
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class,'recruitment_script_status');
    }
}
