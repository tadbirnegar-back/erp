<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\CustomerMS\app\Models\Customer;
use Modules\LMS\Database\factories\StudentFactory;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected static function newFactory(): StudentFactory
    {
        //return StudentFactory::new();
    }

    public function customer(): MorphOne
    {
        return $this->morphOne(Customer::class,'customerable');
    }
}
