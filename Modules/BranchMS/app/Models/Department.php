<?php

namespace Modules\BranchMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BranchMS\Database\factories\DepartmentFactory;
use Modules\StatusMS\app\Models\Status;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected static function newFactory(): DepartmentFactory
    {
        //return DepartmentFactory::new();
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
