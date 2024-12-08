<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\OunitCatsFactory;

class OunitCat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'ounit_cats';

    protected $fillable = ['id', 'name'];


    public function oucProperties()
    {
        return $this->hasMany(OucProperty::class, 'ounit_cat_id', 'id');
    }
}
