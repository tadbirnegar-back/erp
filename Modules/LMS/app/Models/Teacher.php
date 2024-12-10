<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\CustomerMS\app\Models\Customer;
use Modules\HRMS\app\Models\WorkForce;
use Modules\LMS\Database\factories\TeacherFactory;
use Modules\PersonMS\app\Models\Person;

class Teacher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['id'];
    public $timestamps = false;

    protected static function newFactory(): TeacherFactory
    {
//        return TeacherFactory::new();
    }

    public function customer(): MorphOne
    {
        return $this->morphOne(Customer::class, 'customerable');
    }

    public function workForce(): MorphOne
    {
        return $this->morphOne(WorkForce::class, 'workforceable');
    }

    public function Person()
    {
        return $this-> hasManyDeep(Person::class, [WorkForce::class],
            ['workforceable_id', 'id'],
            ['id', 'person_id']
        );
    }
}
