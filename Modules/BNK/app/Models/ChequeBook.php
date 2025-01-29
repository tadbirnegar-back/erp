<?php

namespace Modules\BNK\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        return $this->belongsToMany(Status::class, 'bnkChequeBook_status', 'cheque_book_id', 'status_id');
    }

    public function latestStatus()
    {
        return $this->hasOneThrough(Status::class, BnkChequeBookStatus::class, 'account_id', 'id', 'id', 'status_id')
            ->orderBy('bnkChequeBook_status.create_date', 'desc');
    }

    public function cheques(): HasMany
    {
        return $this->hasMany(Cheque::class, 'cheque_book_id');
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
