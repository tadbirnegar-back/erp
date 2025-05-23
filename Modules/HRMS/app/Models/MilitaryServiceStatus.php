<?php

namespace Modules\HRMS\app\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MilitaryServiceStatus extends Model
{

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): MilitaryServiceStatusFactory
    {
        //return MilitaryServiceStatusFactory::new();
    }
}
