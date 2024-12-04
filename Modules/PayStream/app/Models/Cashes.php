<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\PayStream\Database\factories\CashesFactory;

class Cashes extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'cashes';

    protected $fillable = ['id', 'receipt_file_id'];

    public $timestamps = false;

    public function psPayments()
    {
        return $this->morphMany(PsPayments::class, 'ps_paymentable');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'cash_user', 'cash_id', 'user_id');
    }

    public function cashUsers()
    {
        return $this->hasMany(CashUser::class, 'cash_id', 'id');
    }
}
