<?php

namespace Modules\PFM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PFM\Database\factories\BookletFactory;
use Modules\StatusMS\app\Models\Status;


class Booklet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'pfm_circular_booklets';

    protected $fillable = [
        'p1', 'p2', 'p3', 'ounit_id', 'pfm_circular_id', 'created_date'
    ];

    public function ounit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'ounit_id');
    }


    public function circular()
    {
        return $this->belongsTo(PfmCirculars::class, 'pfm_circular_id');
    }

    public function latestStatus()
    {
        return $this->belongsToMany(Status::class, 'pfm_booklet_statuses', 'booklet_id', 'status_id')
            ->orderByDesc('id')
            ->take(1);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'pfm_booklet_statuses', 'booklet_id', 'status_id');
    }

    public $timestamps = false;

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
