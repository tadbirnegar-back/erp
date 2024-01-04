<?php

namespace Modules\StatusMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BranchMS\app\Models\Branch;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\Database\factories\StatusFactory;

class Status extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): StatusFactory
    {
        //return StatusFactory::new();
    }

    public function persons()
    {
        return $this->belongsToMany(Person::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }
}
