<?php

namespace Modules\CustomerMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\CustomerMS\Database\factories\ShoppingCustomerFactory;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;

class ShoppingCustomer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): ShoppingCustomerFactory
    {
        //return ShoppingCustomerFactory::new();
    }

    public function customer(): MorphOne
    {
        return $this->morphOne(Customer::class,'customerable');
    }

    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    public function natural()
    {
//        return $this->hasOneDeep(Natural::class, [Customer::class, Person::class],
//            ['id', 'personable_id', 'id'],
//            ['id', 'person_id', 'personable_id']);

        return $this->hasOneDeep(
            Natural::class,
            [Customer::class, Person::class], // Intermediate models
            ['id', 'id', 'person_id'], // Foreign keys on the intermediate tables
            ['customerable_id', 'person_id', 'id'] // Local keys on the parent models
        );
    }
}
