<?php

namespace Modules\BranchMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BranchMS\Database\factories\SectionFactory;

class Section extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): SectionFactory
    {
        //return SectionFactory::new();
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
