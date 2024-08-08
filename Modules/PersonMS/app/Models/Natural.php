<?php

namespace Modules\PersonMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AddressMS\app\Models\Address;
use Modules\FileMS\app\Models\File;
use Modules\PersonMS\Database\factories\NaturalFactory;

class Natural extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'mobile',
        'phone_number',
        'father_name',
        'birth_date',
        'bc_code',
        'job',
        'isMarried',
        'level_of_spouse_education',
        'spouse_first_name',
        'spouse_last_name',
        'home_address_id',
        'job_address_id',
        'gender_id',
        'bc_issue_date',
        'bc_issue_location',
        'birth_location',
        'bc_serial',
        'religion_id',
        'religion_type_id',
    ];
    public $timestamps = false;

    protected static function newFactory(): NaturalFactory
    {
        //return NaturalFactory::new();
    }

    public function person()
    {
        return $this->morphOne(Person::class, 'personable');
    }

    public function homeAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'home_address_id');
    }

    public function jobAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'job_address_id');
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class,'religion_id');
    }

    public function religionType()
    {
        return $this->belongsTo(ReligionType::class,'religion_type_id');
    }


}
