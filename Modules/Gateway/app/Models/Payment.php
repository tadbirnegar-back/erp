<?php

namespace Modules\Gateway\app\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AAA\app\Models\User;
use Modules\Gateway\Database\factories\PaymentFactory;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected static function newFactory(): PaymentFactory
    {
        //return PaymentFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    use \Znck\Eloquent\Traits\BelongsToThrough;

    public function person()
    {
        return $this->belongsToThrough(Person::class, [User::class]);
    }

    public static function GetAllStatuses(): Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

}
