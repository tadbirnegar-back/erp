<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\AAA\app\Models\Role;
use Modules\BranchMS\app\Models\Section;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\HRMS\Database\factories\PositionFactory;
use Modules\LMS\app\Models\CourseEmployeeFeature;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\StatusMS\app\Models\Status;

class Position extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): PositionFactory
    {
        //return PositionFactory::new();
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'recruitment_scripts');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(Level::class, 'level_position');
    }


    public function organizationUnits(): BelongsToMany
    {
        return $this->belongsToMany(OrganizationUnit::class, 'ounit_position', 'position_id', 'ounit_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'position_role');
    }

    public function getOunitCatAttribute($value)
    {

        if (is_null($value)) {
            return null;
        }
        $enumInstance = OunitCategoryEnum::tryFrom($value);
        return $enumInstance?->getLabelAndValue();
    }

    public function getOunitCatEnumAttribute($value)
    {

        if (is_null($value)) {
            return null;
        }
        $enumInstance = OunitCategoryEnum::tryFrom($value);
        return $enumInstance;
    }


    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function features()
    {
        return $this->morphMany(CourseEmployeeFeature::class, 'propertyble');
    }
}
