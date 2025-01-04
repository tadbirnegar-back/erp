<?php

namespace Modules\ACMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ACMS\Database\factories\CircularItemFactory;

class CircularItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'circular_id',
        'subject_id',
    ];

    public $timestamps = false;
    protected $table = 'bgt_circular_items';

    public function subject(): BelongsTo
    {
        return $this->belongsTo(CircularSubject::class);
    }
}
