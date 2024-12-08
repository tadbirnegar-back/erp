<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\PayStream\Database\factories\CashUserFactory;

class CashUser extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'cash_user';

    protected $fillable = ['cash_id', 'id', 'user_id'];

    public $timestamps = false;

    public function cash()
    {
        return $this->belongsTo(Cashes::class, 'cash_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
