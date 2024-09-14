<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\EMS\Database\factories\MeetingMemberFactory;

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

}
