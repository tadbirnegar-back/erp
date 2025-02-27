<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\EVAL\Database\factories\EvalCircularSectionFactory;

class EvalCircularSection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = ['id',
        'title',
        'eval_circular_id',
    ];

    public $timestamps = false;


    public function evalCircular()
    {
        return $this->belongsTo(EvalCircular::class, 'eval_circular_id', 'id');
    }

    public function evalCircularIndicators()
    {
        return $this->hasMany(EvalCircularIndicator::class, 'eval_circular_section_id', 'id');
    }
}
