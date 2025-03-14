<?php

namespace Modules\BNK\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BNK\Database\factories\BnkChequeBookStatusFactory;

class BnkChequeBookStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'bnkChequeBook_status';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

}
