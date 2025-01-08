<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AAA\app\Models\User;
use Modules\LMS\Database\factories\CommentFactory;
use Modules\PersonMS\app\Models\Person;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'commentable_id',
        'commentable_type',
        'creator_id',
        'create_date',
        'id',
        'text'
    ];
    protected $table = 'comments';
    public $timestamps = false;

    public function commentable()
    {
        return $this->morphTo();
    }

    use HasRelationships;
    public function person()
    {
        return $this->hasOneDeep(
            Person::class,
            [User::class],
            ['user_id', 'person_id'],
            ['id', 'id']
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class , 'creator_id' , 'id');
    }


}
