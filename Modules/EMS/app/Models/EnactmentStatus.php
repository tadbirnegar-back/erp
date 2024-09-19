<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\AAA\app\Models\User;
use Modules\EMS\Database\factories\EnactmentStatusFactory;
use Modules\FileMS\app\Models\File;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Morilog\Jalali\CalendarUtils;
use Znck\Eloquent\Traits\BelongsToThrough;

class EnactmentStatus extends Pivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'enactment_status';


    use BelongsToThrough;

    public function person()
    {

        return $this->belongsToThrough(Person::class, User::class, foreignKeyLookup: [
            User::class => 'operator_id',
            Person::class => 'person_id',
        ]);
    }


    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function meetingMember(): BelongsTo
    {
        return $this->belongsTo(MeetingMember::class, 'operator_id', 'employee_id')
            ->with(['person', 'mr']);
    }

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(File::class, 'attachment_id');
    }

    public function createDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $jalali = CalendarUtils::strftime('Y/m/d', strtotime($value)); // 1395-02-19
                $jalaliPersianNumbers = CalendarUtils::convertNumbers($jalali); // ۱۳۹۵-۰۲-۱۹

                return $jalaliPersianNumbers;
            },

        );
    }
}
