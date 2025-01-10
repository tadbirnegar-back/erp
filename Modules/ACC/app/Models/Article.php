<?php

namespace Modules\ACC\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ACC\Database\factories\ArticleFactory;

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

    ];

    public $timestamps = false;
    protected $table = 'acc_articles';

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }


}
