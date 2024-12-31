<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\ACMS\app\Scopes\ActiceOnlyScope;
use Modules\ACMS\Database\factories\CircularSubjectFactory;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class CircularSubject extends Model
{
    use HasRecursiveRelationships;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'isActive',
        'old_item_id',
        'parent_id',
        'create_date',
        'code',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ActiceOnlyScope);
    }

    public $timestamps = false;
    protected $table = 'bgt_circular_subjects';
}
