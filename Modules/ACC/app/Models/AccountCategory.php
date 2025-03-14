<?php

namespace Modules\ACC\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ACC\Database\factories\AccountCategoryFactory;

class AccountCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'acc_account_categories';

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'category_id');
    }
}
