<?php

namespace Modules\FormGMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\FormGMS\Database\factories\FieldsFactory;

class Fields extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): FieldsFactory
    {
        //return FieldsFactory::new();
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function fieldType(): BelongsTo
    {
        return $this->belongsTo(FieldType::class,'type_id');
    }
}
