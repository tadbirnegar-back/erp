<?php

namespace Modules\OUnitMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\OUnitMS\Database\factories\DepartmentFactory;

class Department extends Model
{
    use HasFactory;

    protected $table = 'department_ofcs';


    protected $fillable = ['id'];


    public function organizationUnit(): MorphOne
    {
        return $this->morphOne(OrganizationUnit::class, 'unitable');
    }

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

}
