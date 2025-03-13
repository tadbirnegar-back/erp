<?php

namespace Modules\BNK\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ACC\app\Models\Account;
use Modules\BNK\app\Http\Enums\BankAccountTypeEnum;
use Modules\BNK\Database\factories\BankAccountFactory;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\StatusMS\app\Models\Status;
use Znck\Eloquent\Traits\BelongsToThrough;

class BankAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'account_number',
        'iban_number',
        'account_type_id',
        'branch_id',
        'ounit_id',
        'register_date',
    ];

    protected $casts = [
        'account_type_id' => BankAccountTypeEnum::class
    ];

    public $timestamps = false;

    protected $table = 'bnk_bank_accounts';

    public function registerDate(): Attribute
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

    public function bankBranch(): BelongsTo
    {
        return $this->belongsTo(BankBranch::class, 'branch_id');
    }

    use BelongsToThrough;

    public function bank()
    {
        return $this->belongsToThrough(Bank::class, BankBranch::class, foreignKeyLookup: [
            BankBranch::class => 'branch_id',
            Bank::class => 'bank_id',
        ]);
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'bnkAccount_status', 'account_id', 'status_id');
    }

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, BnkAccountStatus::class, 'account_id', 'id', 'id', 'status_id')
            ->orderBy('bnkAccount_status.create_date', 'desc');
    }

    public function chequeBooks(): HasMany
    {
        return $this->hasMany(ChequeBook::class, 'account_id');
    }

    public function accountCards(): HasMany
    {
        return $this->hasMany(BankAccountCard::class, 'account_id');
    }

    public function ounit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'ounit_id');
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'entity_id')->where('entity_type', self::class);
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
