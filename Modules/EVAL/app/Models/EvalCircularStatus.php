<?php

namespace Modules\EVAL\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\EVAL\Database\factories\EvalCircularStatusFactory;
use Modules\StatusMS\app\Models\Status;

class EvalCircularStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'status_id',
        'eval_circular_id',
    ];

    public $timestamps = false;
    public function evalCircular()
    {
        return $this->belongsTo(EvalCircular::class, 'eval_circular_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }


}
