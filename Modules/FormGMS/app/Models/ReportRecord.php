<?php

namespace Modules\FormGMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\FormGMS\Database\factories\ReportRecordFactory;
use Znck\Eloquent\Relations\BelongsToThrough;

class ReportRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): ReportRecordFactory
    {
        //return ReportRecordFactory::new();
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    use \Znck\Eloquent\Traits\BelongsToThrough;

    public function form(): BelongsToThrough
    {
        return $this->belongsToThrough(Form::class, Report::class);
    }
}
