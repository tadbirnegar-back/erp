<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\AAA\app\Models\Role;
use Modules\AAA\app\Models\User;
use Modules\AAA\app\Models\UserRole;
use Modules\EMS\Database\factories\MeetingMemberFactory;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\WorkForce;
use Modules\PersonMS\app\Models\Person;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class MeetingMember extends Pivot
{
//    use EagerLoadPivotTrait;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'meeting_members';

    public function mr(): BelongsTo
    {
        return $this->belongsTo(MR::class);
    }

    use HasRelationships;

    public function person()
    {
        return $this->hasOneDeep(
            Person::class,
            [
//                MeetingMember::class,
                Employee::class,
                Workforce::class,
            ],
            [
//                'meeting_id', // Foreign key on the meeting_members table...
                'id', // Foreign key on the employees table...
                'workforceable_id', // Foreign key on the workforces table...
                'id' // Foreign key on the workforces table...
            ],
            [
//                'id', // Local key on the meetings table...
                'employee_id', // Local key on the meeting_members table...
                'id', // Local key on the employees table...
                'person_id' // Local key on the workforces table...
            ]
        );
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

}
