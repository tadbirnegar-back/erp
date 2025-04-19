<?php

namespace Modules\PFM\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PFM\Database\factories\LevyItemFactory;
use Modules\StatusMS\app\Models\Status;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;


class LevyItem extends Model
{
    use HasFactory, HasRelationships;

    /**
     * The attributes that are mass assignable.
     */

    protected $table = 'pfm_levy_items';

    protected $fillable = [
        'circular_levy_id', 'name', 'created_date', 'id'
    ];


    public $timestamps = false;

    public function tarrifs()
    {
        return $this->hasMany(Tarrifs::class , 'item_id' , 'id');
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
