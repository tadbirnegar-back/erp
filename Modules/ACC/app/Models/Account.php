<?php

namespace Modules\ACC\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Scopes\ActiveAccountOnlyScope;
use Modules\ACC\Database\factories\AccountFactory;
use Modules\BNK\app\Models\Cheque;
use Modules\BNK\app\Models\ChequeBook;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Account extends Model
{
    use HasRecursiveRelationships, AccountTrait;

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
        'new_chain_code',
    ];

    public $timestamps = false;
    protected $table = 'acc_accounts';

    protected static function booted()
    {
        static::addGlobalScope(new ActiveAccountOnlyScope());
    }

    public function scopeActiveInactive($query)
    {
        return $query->whereIntegerInRaw('status_id', [
            $this->activeAccountStatus()->id,
            $this->inactiveAccountStatus()->id,
        ]);
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

    public function descendantsArticles()
    {
        return $this->hasManyOfDescendantsAndSelf(Article::class, 'account_id');
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function newCode()
    {
        return $this->hasOne(Account::class, 'chain_code', 'new_chain_code');
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
