<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\EMS\Database\factories\MRFactory;

class MR extends Model
{
//    use EagerLoadPivotTrait;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'mrs';

}
