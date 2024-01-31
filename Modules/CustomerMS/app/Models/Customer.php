<?php

namespace Modules\CustomerMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AAA\app\Models\User;
use Modules\CustomerMS\Database\factories\CustomerFactory;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

//    protected static function newFactory(): CustomerFactory
//    {
//        //return CustomerFactory::new();
//    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'creator_id');
    }

    public function customerType(): BelongsTo
    {
        return $this->belongsTo(CustomerType::class,'customer_type_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class,'person_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class,'status_id');
    }
}
