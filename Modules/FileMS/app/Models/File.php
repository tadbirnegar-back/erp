<?php

namespace Modules\FileMS\app\Models;

use Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Modules\EVAL\app\Models\EvalCircular;
use Modules\FileMS\Database\factories\FileFactory;
use Modules\StatusMS\app\Models\Status;
use URL;
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
        'isPrivate' => 'boolean',
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
        if ($this->isPrivate) {
            $domain = URL::to('/');
            $value=str_replace('/', '-', $value);
            $response = Http::get($domain . '/api/v1/local/temp/' . $value);

            $result= $response->body();
            $decoded= json_decode($result, true);
//            dd($decoded);
            return $decoded;
        }
        return url('/') . '/' . $value;
    }

    public function getSizeAttribute($value)
    {
        return  Number::fileSize($value);
    }

    public function EvalCirculars()
    {
        return $this->hasmany(EvalCircular::class, 'file_id', 'id');
    }
}
