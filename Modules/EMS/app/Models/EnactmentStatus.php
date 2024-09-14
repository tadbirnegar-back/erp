<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\AAA\app\Models\User;
use Modules\EMS\Database\factories\EnactmentStatusFactory;
use Znck\Eloquent\Traits\BelongsToThrough;

class EnactmentStatus extends Pivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'enactment_status';


    use BelongsToThrough;

//    public function person()
//    {
//
//        return $this->belongsToThrough(Person::class, User::class, foreignKeyLookup: [
//            User::class => 'operator_id',
//            Person::class => 'person_id',
//        ]);
//    }


    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
