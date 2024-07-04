<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\AAA\app\Models\User;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\Gateway\app\Models\Payment;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Models\RecruitmentScript;
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
        return $this->belongsTo(User::class,'head_id');
    }

    public function evaluations(): BelongsToMany
    {
        return $this->belongsToMany(Evaluation::class);
    }

    use BelongsToThrough;

    public function person()
    {
        return $this->belongsToThrough(Person::class, User::class,foreignKeyLookup: [
            User::class => 'head_id',
            Person::class => 'person_id'
        ]);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'organization_unit_id');
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class,'recruitment_script_status');
    }

    public function recruitmentScripts(): HasMany
    {
        return $this->hasMany(RecruitmentScript::class, 'organization_unit_id');
    }


    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class,'ounit_position','ounit_id','position_id');
    }

    public static function GetAllStatuses(): Collection
    {
        return Status::all()->where('model',  '=', self::class);
    }
}
