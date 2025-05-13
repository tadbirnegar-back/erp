<?php

namespace Modules\PFM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\FileMS\app\Models\File;
use Modules\PFM\Database\factories\PfmCircularsFactory;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;


class PfmCirculars extends Model
{
    use HasFactory, HasRelationships;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'pfm_circulars';

    protected $fillable = [
        'name',
        'description',
        'fiscal_year_id',
        'file_id',
        'created_date',
        'start_date',
        'end_date',
    ];

    public $timestamps = false;

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function latestStatus()
    {
        return $this->belongsToMany(Status::class, 'pfm_circular_statuses', 'pfm_circular_id', 'status_id')
            ->orderByDesc('id')
            ->take(1);
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }
    public function levies()
    {
        return $this->HasManyDeep(
            Levy::class,
            [LevyCircular::class],
            ['circular_id', 'id'],
            ['id', 'levy_id'],
        )->select('pfm_levies.id', 'pfm_levies.name');
    }

    public function booklets()
    {
        return $this->HasMany(Booklet::class, 'pfm_circular_id');
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'pfm_circular_statuses', 'pfm_circular_id', 'status_id');
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
