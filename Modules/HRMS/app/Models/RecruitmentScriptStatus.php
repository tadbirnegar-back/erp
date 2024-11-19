<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Observers\ScriptStatusObserver;
use Modules\HRMS\Database\factories\RecruitmentScriptStatusFactory;
use Modules\PersonMS\app\Models\Person;
use Modules\StatusMS\app\Models\Status;
use Znck\Eloquent\Traits\BelongsToThrough;

class RecruitmentScriptStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;
    protected $table = 'recruitment_script_status';


    protected $fillable = [
        'recruitment_script_id',
        'status_id',
        'create_date',
        'description',
        'operator_id',
    ];

    protected static function boot()
    {
        parent::boot();
        // Register the observer
        static::observe(ScriptStatusObserver::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    use BelongsToThrough;

    public function person()
    {
        return $this->belongsToThrough(Person::class, User::class, foreignKeyLookup: [
            User::class => 'operator_id',
        ]);
    }

}
