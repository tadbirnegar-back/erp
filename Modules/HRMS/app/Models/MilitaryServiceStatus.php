<?php

namespace Modules\HRMS\app\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class MilitaryServiceStatus extends Model
{

    use HasRelationships;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;

    protected static function newFactory(): MilitaryServiceStatusFactory
    {
        //return MilitaryServiceStatusFactory::new();
    }

    public function militaryService(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MilitaryService::class);
    }
}
