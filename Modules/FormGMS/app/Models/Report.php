<?php

namespace Modules\FormGMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AAA\app\Models\User;
use Modules\FormGMS\Database\factories\ReportFactory;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): ReportFactory
    {
        //return ReportFactory::new();
    }

    public function reportRecords(): HasMany
    {
        return $this->hasMany(ReportRecord::class, 'report_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class,'creator_id');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
