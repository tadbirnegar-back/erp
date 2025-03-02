<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\EVAL\Database\factories\EvalEvaluationFactory;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\StatusMS\app\Models\Status;

class EvalEvaluation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'average',
        'sum',
        'create_date',
        'creator_id',
        'parent_id',
        'evaluator_id',
        'id',
        'is_revised',
        'target_ounit_id',
        'average',
        'description',
        'title',
        'eval_circular_id',
        'evaluator_ounit_id',
    ];

    public $timestamps = false;

    public function evaluationAnswers()
    {
        return $this->hasMany(EvalEvaluationAnswer::class, 'eval_evaluation_id', 'id');
    }

    public function evalCircular()
    {
        return $this->belongsTo(EvalCircular::class, 'eval_circular_id', 'id');
    }

    public function targetOunits()
    {
        return $this->belongsTo(OrganizationUnit::class, 'target_ounit_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(OrganizationUnit::class, 'parent_id', 'id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id', 'id');

    }

    public function evalEvaluationStatus()
    {
        return $this->hasMany(EvalEvaluationStatus::class, 'eval_evaluation_id', 'id');
    }

    public function lastStatusOfEvaluation()
    {
        return $this->hasOne(EvalEvaluationStatus::class, 'eval_evaluation_id', 'id')
            ->orderBy('created_at', 'desc')->take(1);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
