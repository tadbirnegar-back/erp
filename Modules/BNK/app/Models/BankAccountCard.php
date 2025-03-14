<?php

namespace Modules\BNK\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\BNK\Database\factories\BankAccountCardFactory;
use Modules\StatusMS\app\Models\Status;

class BankAccountCard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'card_number',
        'expire_date',
        'account_id',
    ];

    public $timestamps = false;
    protected $table = 'bnk_account_cards';

    public function expireDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                // Convert to Jalali
                $dateTimeString = convertGregorianToJalali($value);

                return $dateTimeString;
            },

            set: function ($value) {
                if (is_null($value)) {
                    return null;
                }
                // Convert to Gregorian
                $dateTimeString = convertJalaliPersianCharactersToGregorian($value);

                return $dateTimeString;
            }
        );
    }

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, BnkAccountCardStatus::class, 'bnkCard_id', 'id', 'id', 'status_id')
            ->orderBy('bnkCard_status.create_date', 'desc');
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'bnkCard_status', 'bnkCard_id', 'status_id');
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }

}
