<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AAA\app\Models\User;
use Modules\EMS\Database\factories\EnactmentReviewFactory;
use Modules\FileMS\app\Models\File;
use Modules\StatusMS\app\Models\Status;
use Morilog\Jalali\CalendarUtils;
use Staudenmeir\EloquentHasManyDeep\HasTableAlias;

class EnactmentReview extends Model
{
    use HasTableAlias;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    public function meetingMembers(): HasMany
    {
        return $this->hasMany(MeetingMember::class, 'employee_id', 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(File::class, 'attachment_id');
    }

    public function enactment(): BelongsTo
    {
        return $this->belongsTo(Enactment::class, 'enactment_id');
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


    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

}
