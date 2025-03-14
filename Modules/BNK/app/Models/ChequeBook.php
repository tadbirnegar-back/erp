<?php

namespace Modules\BNK\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ACC\app\Models\Account;
use Modules\BNK\Database\factories\ChequeBookFactory;
use Modules\StatusMS\app\Models\Status;

class ChequeBook extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'cheque_series',
        'cheque_count',
        'account_id',
        'creator_id',
        'create_date',
    ];

    public $timestamps = false;
    protected $table = 'bnk_cheque_books';


    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'bnkChequeBook_status', 'chequeBook_id', 'status_id');
    }

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, BnkChequeBookStatus::class, 'chequeBook_id', 'id', 'id', 'status_id')
            ->orderBy('bnkChequeBook_status.create_date', 'desc');
    }

    public function cheques(): HasMany
    {
        return $this->hasMany(Cheque::class, 'cheque_book_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'entity_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'account_id');
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
