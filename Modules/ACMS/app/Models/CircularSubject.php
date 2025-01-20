<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'subject_type_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ActiceOnlyScope);
    }

    public $timestamps = false;
    protected $table = 'bgt_circular_subjects';

    public function circulars(): BelongsToMany
    {
        return $this->belongsToMany(Circular::class, 'bgt_circular_items', 'subject_id', 'circular_id')
            ->withPivot('bgt_circular_items.percentage');
    }
}
