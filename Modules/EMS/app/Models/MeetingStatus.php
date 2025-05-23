<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\EMS\Database\factories\MeetingStatusFactory;

class MeetingStatus extends Pivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'meeting_status';

}
