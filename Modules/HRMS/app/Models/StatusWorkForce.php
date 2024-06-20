<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HRMS\Database\factories\StatusWorkForceFactory;
use Modules\StatusMS\app\Models\Status;

class StatusWorkForce extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];
protected $table='status_work_force';
    public $timestamps = false;

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

}
