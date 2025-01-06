<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\CourseTargetFactory;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class CourseTarget extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'course_targets';

    protected $fillable = ['id', 'parent_ounit_id' , 'course_id'];

    public function ounitFeatures()
    {
        return $this->hasMany(CourseOunitFeature::class, 'course_target_id', 'id');
    }

    public function ounit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'parent_ounit_id', 'id');
    }

    public function employeeFeatures()
    {
        return $this->hasMany(CourseEmployeeFeature::class, 'course_target_id', 'id');
    }

}
