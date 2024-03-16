<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\HRMS\Database\factories\EmployeeFactory;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): EmployeeFactory
    {
        //return EmployeeFactory::new();
    }

    public function workForce(): MorphOne
    {
        return $this->morphOne(WorkForce::class,'workforceable');
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class,'employee_position');
    }

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(Level::class,'employee_level');
    }
}
