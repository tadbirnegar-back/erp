<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\EVAL\Database\factories\EvalCircularFactory;
use Modules\Eval\app\Http\Enums\EvalCircularStatusEnum;
use Modules\FileMS\app\Models\File;
use Modules\StatusMS\app\Models\Status;

class EvalCircular extends Model
{
    use HasFactory;

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

    public function CircularStatus()
    {
        return $this->lessons()->whereExists(function ($query) {
            $query->select(\DB::raw(1))
                ->from('evalEvaluation_status as es')
                ->join('statuses as s', 'es.status_id', '=', 's.id')
                ->whereColumn('es.eval_evaluation_id ', 'eval_evaluations.id')
                ->where('s.name', EvalCircularStatusEnum::ACTIVE->value)
                ->where('es.created_at', function ($subQuery) {
                    $subQuery->selectRaw('MAX(created_at)')
                        ->from('evalEvaluation_status as sub_ls')
                        ->whereColumn('sub_es.eval_evaluation_id ', 'es.eval_evaluation_id ');
                });
        });
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

}
