<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\FileMS\app\Models\File;

class EvalCircularVariable extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = ['id',
        'title',
        'eval_circular_indicator_id',
        'weight'
    ];

    public $timestamps = false;


    public function evalCircularIndicator()
    {
        return $this->belongsTo(EvalCircularIndicator::class, 'eval_circular_indicator_id', 'id');
    }

    public function evalVariableTargets()
    {
        return $this->hasMany(EvalVariableTarget::class, 'eval_circular_variables_id', 'id');
    }

    public function files()
    {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }
}
