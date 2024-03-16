<?php

namespace Modules\FormGMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\FormGMS\Database\factories\FieldsFactory;

class Field extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected $casts = ['is_required' => 'boolean'];

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

    use \Znck\Eloquent\Traits\BelongsToThrough;

    public function form()
    {
        return $this->belongsToThrough(Form::class, Part::class);
    }

    public function reportRecords(): HasMany
    {
        return $this->hasMany(ReportRecord::class, 'field_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

}
