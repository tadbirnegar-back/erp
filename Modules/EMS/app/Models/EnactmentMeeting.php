<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\EMS\Database\factories\EnactmentMeetingFactory;

class EnactmentMeeting extends pivot
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        // Register the observer
        static::observe(\Modules\EMS\app\Observers\EnactmentMeetingObserver::class);
    }
    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'enactment_meeting';


}
