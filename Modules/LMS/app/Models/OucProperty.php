<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\OucPropertyFactory;

class OucProperty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'ouc_properties';

    protected $fillable = ['id', 'column_name', 'name', 'ounit_cat_id'];

    public function ounitCat()
    {
        return $this->belongsTo(OunitCat::class, 'ounit_cat_id', 'id');
    }

    public function values()
    {
        return $this->hasMany(OucPropertyValue::class, 'ouc_property_id', 'id');
    }

    public function lastValue()
    {
        return $this->hasOne(OucPropertyValue::class, 'ouc_property_id', 'id')->latest('id');
    }

}
