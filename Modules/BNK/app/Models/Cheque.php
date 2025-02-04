<?php

namespace Modules\BNK\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\BNK\Database\factories\ChequeFactory;
use Modules\StatusMS\app\Models\Status;

class Cheque extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'payee_name',
        'segment_number',
        'amount',
        'cheque_book_id',
        'due_date',
        'signed_date',
    ];

    public $timestamps = false;
    protected $table = 'bnk_cheques';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, BnkChequeStatus::class, 'cheque_id', 'id', 'id', 'status_id')
            ->orderBy('bnkCheque_status.create_date', 'desc');
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'bnkCheque_status', 'cheque_id', 'status_id');
    }

    public function chequeBook(): BelongsTo
    {
        return $this->belongsTo(ChequeBook::class, 'cheque_book_id');
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
