<?php

namespace Modules\SMM\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\FileMS\app\Models\File;
use Modules\SMM\Database\factories\CircularFactory;
use Modules\StatusMS\app\Models\Status;


class Circular extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'title',
        'description',
        'file_id',
        'fiscal_year_id',
        'min_wage',
        'marriage_benefit',
        'rent_benefit',
        'grocery_benefit',
    ];

    public $timestamps = false;
    protected $table = 'smm_circulars';

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'smmCircular_status', 'circular_id', 'status_id');
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function scopeLatestStatus($query)
    {
        return $query->join('smmCircular_status', 'smm_circulars.id', '=', 'smmCircular_status.circular_id')
            ->join('statuses', 'smmCircular_status.status_id', '=', 'statuses.id')
            ->whereRaw('smmCircular_status.create_date = (SELECT MAX(create_date) FROM smmCircular_status WHERE circular_id = smm_circulars.id)');

    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
