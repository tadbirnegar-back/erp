<?php

namespace Modules\CustomerMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\AAA\app\Models\User;
use Modules\CustomerMS\Database\factories\CustomerFactory;
use Modules\FileMS\app\Models\File;
use Modules\LMS\app\Models\Enroll;
use Modules\PayStream\app\Models\Order;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Customer extends Model
{
    use HasFactory;
    use HasRelationships;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "creator_id",
        "person_id",
        "status_id",
        "create_date",
        "customerable_id",
        "customerable_type",
        "customer_type_id",
    ];
    public $timestamps = false;

//    protected static function newFactory(): CustomerFactory
//    {
//        //return CustomerFactory::new();
//    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function customerType(): BelongsTo
    {
        return $this->belongsTo(CustomerType::class, 'customer_type_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function customerable(): MorphTo
    {
        return $this->morphTo();
    }

    public function enrolls()
    {
        return $this->hasManyDeep(Enroll::class, [Order::class],
            ['customer_id', 'id'],
            ['id', 'orderable_id']
        );
    }

    public function avatar()
    {
        return $this->hasOneThrough(
            File::class,
            Person::class,
            'id',
            'id',
            'person_id',
            'profile_picture_id'
        );
    }
}
