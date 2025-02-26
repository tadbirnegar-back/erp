<?php

namespace Modules\BNK\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\BNK\Database\factories\TransactionFactory;
use Modules\StatusMS\app\Models\Status;


class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'deposit',
        'withdrawal',
        'transfer',
        'transactionable_id',
        'transactionable_type',
        'bank_account_id',
        'creator_id',
        'cheque_id',
        'card_id',
        'status_id',
        'isSynced',
        'create_date',
        'tracking_code',
    ];
    public $timestamps = false;
    protected $table = 'bnk_transactions';

    public function cheque(): BelongsTo
    {
        return $this->belongsTo(Cheque::class, 'cheque_id');
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
