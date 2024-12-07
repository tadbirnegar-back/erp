<?php

namespace Modules\PayStream\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\PayStream\Database\factories\CardToCardUserFactory;

class CardToCardUser extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'card_to_card_user';

    protected $fillable = ['card_to_card_id', 'id', 'user_id'];

    public $timestamps = false;

    public function cardToCard()
    {
        return $this->belongsTo(CardToCards::class, 'card_to_card_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
