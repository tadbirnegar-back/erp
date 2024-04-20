<?php

namespace Modules\BranchMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BranchMS\Database\factories\SectionFactory;
use Modules\StatusMS\app\Models\Status;
use Znck\Eloquent\Traits\BelongsToThrough;

class Section extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): SectionFactory
    {
        //return SectionFactory::new();
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    use BelongsToThrough;

    public function branch()
    {
        return $this->belongsToThrough(Branch::class, Department::class);
    }
}
