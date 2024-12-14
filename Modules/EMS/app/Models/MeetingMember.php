<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\AAA\app\Models\Role;
use Modules\AAA\app\Models\User;
use Modules\AAA\app\Models\UserRole;
use Modules\EMS\Database\factories\MeetingMemberFactory;
use Modules\PersonMS\app\Models\Person;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Znck\Eloquent\Traits\BelongsToThrough;

class MeetingMember extends Pivot
{
//    use EagerLoadPivotTrait;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'employee_id',
        'meeting_id',
        'mr_id',
    ];

    public $timestamps = false;
    protected $table = 'meeting_members';

    public function mr(): BelongsTo
    {
        return $this->belongsTo(MR::class);
    }

    use HasRelationships, BelongsToThrough;

    public function person()
    {
        return $this->belongsToThrough(Person::class, [User::class],
            foreignKeyLookup: [
                Person::class => 'person_id',
                User::class => 'employee_id',
            ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function roles()
    {
        return $this->hasManyDeep(Role::class, [
            User::class,
            UserRole::class,
        ],
            [
                'id',
                'user_id',
                'id',
            ],
            [
                'employee_id',
                'id',
                'role_id',
            ]
        );
    }

    public function enactmentReviews()
    {
        return $this->hasOne(EnactmentReview::class, 'user_id', 'employee_id');

//        return $this->hasManyDeep(EnactmentReview::class, [
//            Enactment::class . ' as e3',
//
//        ],
//            [
//                'id',
//                'user_id',
//            ],
//            [
//                'enactment_id',
//                'employee_id',
//
//            ]
//        );
    }

    public function enactmentReviewss()
    {
        return $this->hasMany(EnactmentReview::class, 'user_id', 'employee_id');

    }

//    public function enactments()
//    {
//        return $this->hasManyDeep(Enactment::class, [
//            EnactmentReview::class,
//        ],
//            [
//                'employee_id',
//                'user_id',
//            ],
//            [
//                'employee_id',
//                'employee_id',
//            ]
//        );
//    }

    public function enactments()
    {
        return $this->hasManyDeep(Enactment::class, [
            Meeting::class],
            [
                'id',
                'meeting_id',
            ],
            [
                'meeting_id',
                'id',
            ]);
    }

}
