<?php

namespace Modules\PersonMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\AddressMS\app\Models\Address;
use Modules\FileMS\app\Models\File;
use Modules\HRMS\app\Models\WorkForce;
use Modules\PersonMS\Database\factories\PersonFactory;
use Modules\StatusMS\app\Models\Status;

class Person extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    protected $table = 'persons';
    public $timestamps = false;
    protected static function newFactory(): PersonFactory
    {
        //return PersonFactory::new();
    }

    public function personable()
    {
        return $this->morphTo();
    }

    public function status()
    {
        return $this->belongsToMany(Status::class)->latest('create_date')->take(1);
    }
    public function statuses()
    {
        return $this->belongsToMany(Status::class)->latest('create_date');
    }

    public function avatar()
    {
        return $this->belongsTo(File::class, 'profile_picture_id');
    }

    public function workForce(): HasOne
    {
        return $this->hasOne(WorkForce::class,'person_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }



    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
