<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\PayStream\Database\factories\ChecksFactory;

class Checks extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'checks';

    protected $fillable = ['id', 'check_file_id', 'due_date', 'serial_number'];

    public $timestamps = false;

    public function psPayments()
    {
        return $this->morphMany(PsPayments::class, 'ps_paymentable');
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'check_user', 'check_id', 'user_id');
    }

    public function checkUsers()
    {
        return $this->hasMany(CashUser::class, 'check_id', 'id');
    }

}
