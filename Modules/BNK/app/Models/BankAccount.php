<?php

namespace Modules\BNK\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\BNK\Database\factories\BankAccountFactory;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\StatusMS\app\Models\Status;

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

    public $timestamps = false;

    protected $table = 'bnk_bank_accounts';

    public function bankBranch(): BelongsTo
    {
        return $this->belongsTo(BankBranch::class, 'branch_id');
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

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

}
