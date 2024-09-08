<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\EMS\Database\factories\MeetingFactory;
use Modules\StatusMS\app\Models\Status;

class Meeting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'title',
        'meeting_detail',
        'meeting_number',
        'isTemplate',
        'summary',
        'creator_id',
        'meeting_type_id',
        'ounit_id',
        'parent_id',
        'create_date',
        'start_time',
        'end_time',
        'invitation_date',
        'meeting_date',
        'reminder_date',
    ];

    public $timestamps = false;

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'meeting_status', 'meeting_id', 'status_id');
    }


    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function meetingDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $jalali = \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($value)); // 1395-02-19
                $jalaliPersianNumbers = \Morilog\Jalali\CalendarUtils::convertNumbers($jalali); // ۱۳۹۵-۰۲-۱۹

                return $jalaliPersianNumbers;
            },

            set: function ($value) {
                $englishJalaliDateString = \Morilog\Jalali\CalendarUtils::convertNumbers($value, true);

                $dateTimeString = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d', $englishJalaliDateString)
                    ->toDateTimeString();

                return $dateTimeString;
            }
        );
    }


}
