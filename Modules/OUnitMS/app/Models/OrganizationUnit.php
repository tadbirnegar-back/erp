<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\AAA\app\Models\User;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\OUnitMS\Database\factories\OrganizationUnitFactory;
use Modules\PersonMS\app\Models\Person;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;
use Znck\Eloquent\Traits\BelongsToThrough;

class OrganizationUnit extends Model
{
    use HasFactory;
    use HasRecursiveRelationships;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
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

    use BelongsToThrough;

    public function person()
    {
        return $this->belongsToThrough(Person::class, User::class);
    }
}
