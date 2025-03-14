<?php

namespace Modules\ACC\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ACC\Database\factories\ArticleFactory;
use Modules\BNK\app\Models\Transaction;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'description',
        'debt_amount',
        'credit_amount',
        'account_id',
        'document_id',
        'transaction_id',

    ];

    public $timestamps = false;
    protected $table = 'acc_articles';

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
