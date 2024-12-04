<?php

namespace Modules\StatusMS\app\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Illuminate\Database\Eloquent\Model;
use Modules\BranchMS\app\Models\Branch;
use Modules\FileMS\app\Models\File;
use Modules\OUnitMS\app\Http\Enums\statusEnum;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\Database\factories\StatusFactory;

class Status extends Model
{
    use eagerLoadPivotTrait;

    public $timestamps = false;
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

    public function files()
    {
        return $this->belongsToMany(File::class);
    }
}
