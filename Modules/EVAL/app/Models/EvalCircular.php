<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AAA\app\Models\User;
use Modules\EVAL\Database\factories\EvalCircularFactory;

class EvalCircular extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'id',
        'created_date',
        'creator_id',
        'description',
        'expired_date',
        'file_id',
        'is_optional',
        'maximum_value',
        'title'
    ];

    public $timestamps = true;

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


}
