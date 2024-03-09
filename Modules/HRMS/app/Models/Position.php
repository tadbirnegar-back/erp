<?php

namespace Modules\HRMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\BranchMS\app\Models\Section;
use Modules\HRMS\Database\factories\PositionFactory;
use Modules\StatusMS\app\Models\Status;

class Position extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): PositionFactory
    {
        //return PositionFactory::new();
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class,'employee_position');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }


    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
