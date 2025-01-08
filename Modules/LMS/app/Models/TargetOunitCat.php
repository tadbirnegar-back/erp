<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\LMS\Database\factories\TargetOunitCatFactory;

class TargetOunitCat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'target_ounit_cat';

    protected $fillable = [
        'course_target_id' ,
        'ounit_cat_id',
        'id'
    ];


    public $timestamps = false;

}
