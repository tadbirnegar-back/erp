<?php

namespace Modules\ACC\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ACC\app\Scopes\ActiveAccountOnlyScope;
use Modules\ACC\Database\factories\AccountFactory;
use Modules\BNK\app\Models\Cheque;
use Modules\BNK\app\Models\ChequeBook;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Account extends Model
{
    use HasRecursiveRelationships;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'segment_code',
        'chain_code',
        'accountable_id',
        'accountable_type',
        'parent_id',
        'ounit_id',
        'category_id',
        'subject_id',
        'status_id',
        'entity_id',
        'entity_type',
    ];

    public $timestamps = false;
    protected $table = 'acc_accounts';

    protected static function booted()
    {
        static::addGlobalScope(new ActiveAccountOnlyScope());
    }

    public function accountCategory(): BelongsTo
    {
        return $this->belongsTo(AccountCategory::class, 'category_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'account_id');
    }

    public function cheques()
    {
        return $this->hasManyThrough(Cheque::class, ChequeBook::class, 'account_id', 'cheque_book_id', 'entity_id', 'id');
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
