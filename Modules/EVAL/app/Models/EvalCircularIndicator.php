<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\EVAL\Database\factories\EvalCircularIndicatorFactory;

class EvalCircularIndicator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = ['id',
        'title',
        'eval_circular_section_id',
        'coefficient',

    ];

    public $timestamps = false;

    public function evalCircularSection()
    {
        return $this->belongsTo(EvalCircularSection::class, 'Eval_circular_section', 'id');
    }

    public function evalCircularVariable()
    {
        return $this->hasMany(EvalCircularVariable::class, 'eval_circular_indicator_id', 'id');
    }

}
