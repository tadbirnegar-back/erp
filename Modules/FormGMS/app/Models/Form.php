<?php

namespace Modules\FormGMS\app\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\FormGMS\Database\factories\FormFactory;
use Modules\StatusMS\app\Models\Status;

class Form extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): FormFactory
    {
        //return FormFactory::new();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'form_id');
    }

    public function fields()
    {
        return $this->hasManyThrough(Field::class, Part::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class,'status_id');
    }

    public static function GetAllStatuses(): Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
