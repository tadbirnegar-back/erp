<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\PrivacyFactory;

class Privacy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'privicies';

    protected $fillable = [
        'id',
        'name'
    ];


    public function courses()
    {
        return $this->hasMany(Course::class, 'privacy_id', 'id');
    }

}
