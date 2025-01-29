<?php

namespace Modules\BNK\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BNK\Database\factories\BnkAccountCardStatusFactory;

class BnkAccountCardStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'bnkCard_status';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

}
