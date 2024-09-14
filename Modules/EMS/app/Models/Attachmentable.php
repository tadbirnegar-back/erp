<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Modules\EMS\Database\factories\AttachmentableFactory;

class Attachmentable extends MorphPivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];
    protected $table = 'attachmentables';

    public $timestamps = false;

}
