<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\HRMS\Database\factories\MilitaryServiceFactory;

class MilitaryService extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'person_id',
        'exemption_type_id',
        'military_service_status_id',
        'work_force_id',
        'issue_date',
    ];

    public $timestamps = false;


    public function militaryServiceStatus(): BelongsTo
    {
        return $this->belongsTo(MilitaryServiceStatus::class);
    }

    public function exemptionType(): BelongsTo
    {
        return $this->belongsTo(ExemptionType::class);
    }

}
