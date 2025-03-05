<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\EVAL\app\Observers\CircularObserver;
use Modules\EVAL\Database\factories\EvalCircularFactory;
use Modules\Eval\app\Http\Enums\EvalCircularStatusEnum;
use Modules\FileMS\app\Models\File;
use Modules\LMS\app\Models\OucProperty;
use Modules\LMS\app\Models\OucPropertyValue;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class EvalCircular extends Model
{
    use HasFactory, HasRelationships;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'id',
        'create_date',
        'creator_id',
        'description',
        'expired_date',
        'file_id',
        'is_optional',
        'maximum_value',
        'title'
    ];

    public $timestamps = false;

    public function evalCircularSections()
    {
        return $this->hasMany(EvalCircularSection::class, 'eval_circular_id', 'id');
    }

    public function evalEvaluations()
    {
        return $this->hasMany(EvalEvaluation::class, 'eval_circular_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'eval_circular_statuses', 'eval_circular_id', 'status_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }

    public function evalCircularStatus()
    {
        return $this->hasMany(EvalCircularStatus::class, 'eval_circular_id', 'id');
    }

    public function variables()
    {
        return $this->hasManyDeep(
            EvalCircularVariable::class,
            [EvalCircularSection::class,EvalCircularIndicator::class ],
            [
                'eval_circular_id',
                'eval_circular_section_id',
                'eval_circular_indicator_id',
            ],
            [
                'id',
                'id',
                'id',
            ]
        );
    }
    protected static function boot()
    {
        parent::boot();

        static::observe(CircularObserver::class);
    }
    public function lastStatusOfEvalCircular()
    {
        return $this->hasOne(EvalCircularStatus::class, 'eval_circular_id', 'id')
            ->orderBy('id', 'desc');
    }
}
