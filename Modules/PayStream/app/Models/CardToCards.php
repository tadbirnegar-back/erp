<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\PayStream\Database\factories\CardToCardsFactory;

class CardToCards extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'card_to_cards';

    protected $fillable = ['receipt_file_id', 'id' , 'reference_number'];

    public $timestamps = false;

    public function psPayments()
    {
        return $this->morphMany(PsPayments::class, 'ps_paymentable');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'card_to_card_user', 'card_to_card_id', 'user_id');
    }

    public function cardToCardsUser()
    {
        return $this->hasMany(CardToCardUser::class, 'card_to_card_id', 'id');
    }

}
