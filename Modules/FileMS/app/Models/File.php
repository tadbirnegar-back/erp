<?php

namespace Modules\FileMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\FileMS\Database\factories\FileFactory;
use Modules\StatusMS\app\Models\Status;
use Znck\Eloquent\Relations\BelongsToThrough;

class File extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected $casts = [
        'created_date' => 'datetime:Y-m-d',
    ];

    protected static function newFactory(): FileFactory
    {
        //return FileFactory::new();
    }

    public function extension(): BelongsTo
    {
        return $this->belongsTo(Extension::class);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'file_status', 'file_id', 'status_id')->withPivot('created_date');
    }

    public function currentStatus()
    {
        return $this->hasOne(FileStatusPivot::class)->latestOfMany();
    }

    public function mimeType(): BelongsToThrough
    {
        return $this->belongsToThrough(MimeType::class, Extension::class, foreignKeyLookup: [
            MimeType::class => 'type_id'
        ]);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function getSlugAttribute($value)
    {
        return url('/') . '/' . $value;
    }
}
