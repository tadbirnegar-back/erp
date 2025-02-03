<?php

namespace Modules\BNK\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BNK\Database\factories\BankBranchFactory;

class BankBranch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'branch_code',
        'address',
        'phone_number',
        'bank_id',
    ];

    public $timestamps = false;
    protected $table = 'bnk_bank_branches';


    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
