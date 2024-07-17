<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HRMS\Database\factories\ConformationTypeScriptTypeFactory;

class ConfirmationTypeScriptType extends Model
{
    use HasFactory;

    /**
     *
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'confirmation_type_script_type';
}
