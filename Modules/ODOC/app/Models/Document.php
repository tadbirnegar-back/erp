<?php

namespace Modules\ODOC\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\ODOC\app\Http\Enums\TypeOfModelsEnum;
use Modules\ODOC\app\Observers\DocumentObserver;
use Modules\ODOC\Database\factories\DocumentFactory;
use Modules\StatusMS\app\Models\Status;


class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'odoc_documents';

    protected $fillable = [
        'component_to_render',
        'data',
        'model',
        'model_id',
        'serial_number',
        'title',
        'created_date',
        'creator_id',
        'version',
        'ounit_id'
    ];

    public $timestamps = false;

    public function model(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value == BuildingDossier::class) {
                    return TypeOfModelsEnum::BuildingDossier->value;
                }
                return null;
            }
        );
    }

    protected static function boot()
    {
        parent::boot();

        // Register the observer
        static::observe(DocumentObserver::class);
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }
}
