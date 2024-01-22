<?php

namespace Modules\AddressMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AddressMS\Database\factories\AddressFactory;
use Modules\StatusMS\app\Models\Status;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected static function newFactory(): AddressFactory
    {
        //return AddressFactory::new();
    }

    public function status()
    {
        return $this->belongsTo(Status::class)->where('model','=',self::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    use \Znck\Eloquent\Traits\BelongsToThrough;

    public function state()
    {
        return $this->belongsToThrough(State::class, City::class);
    }
    public function country()
    {
        return $this->belongsToThrough(Country::class, [State::class, City::class]);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
