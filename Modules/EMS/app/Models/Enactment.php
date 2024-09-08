<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\EMS\app\Http\Enums\EnactmentReviewEnum;
use Modules\EMS\Database\factories\EnactmentFactory;
use Modules\StatusMS\app\Models\Status;

class Enactment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'custom_title',
        'description',
        'rejection_reason',
        'auto_serial',
        'serial',
        'title_id',
        'creator_id',
        'meeting_id',
        'rejection_file_id',
        'create_date',
    ];

    protected $appends = ['upshot'];

    public $timestamps = false;


    public function attachments(): MorphToMany
    {
        return $this->morphToMany(Attachmentable::class, 'attachmentable')->withPivot('title');
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'enactment_status', 'enactment_id', 'status_id');
    }

    public function status()
    {
        return $this->hasOneThrough(Status::class, EnactmentStatus::class, 'enactment_id', 'id', 'id', 'status_id')->orderBy('enactment_status.create_date', 'desc');
    }


    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function enactmentReviews(): HasMany
    {
        return $this->hasMany(EnactmentReview::class, 'enactment_id');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(EnactmentTitle::class);
    }

    public function reviewStatuses()
    {
        return $this->hasManyThrough(Status::class, EnactmentReview::class, 'enactment_id', 'id', 'id', 'status_id');
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }

    public function getUpshotAttribute()
    {
        if (!$this->relationLoaded('reviewStatuses')) {
            return null;
        }

        $reviewStatuses = $this->reviewStatuses;

        if ($reviewStatuses->count() < 3) {
            return EnactmentReview::GetAllStatuses()->firstWhere('name', EnactmentReviewEnum::UNKNOWN->value);
        }

        $result = $reviewStatuses->groupBy('id')
            ->map(fn($statusGroup) => [
                'status' => $statusGroup->first(),
                'count' => $statusGroup->count()
            ])
            ->sortByDesc('count')
            ->values();

        return $result[0]['status'];
    }
}
