<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    protected $fillable = ['confirmation_type_id',
        'script_type_id',
        'option_id',
        'priority',
        'option_type',];
}
