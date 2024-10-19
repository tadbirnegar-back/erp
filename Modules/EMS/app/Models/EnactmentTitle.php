<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\EMS\app\Http\Traits\EnactmentTitleTrait;
use Modules\EMS\app\Scopes\ActiveStatusScope;
use Modules\EMS\Database\factories\EnactmentTitleFactory;
use Modules\StatusMS\app\Models\Status;

class EnactmentTitle extends Model
{
    use EnactmentTitleTrait;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected static function booted()
    {
        static::addGlobalScope(new ActiveStatusScope());
    }

    public static function GetAllStatuses()
    {
        return Status::where('model', self::class);
    }

    public function delete()
    {
        $this->status_id = $this->enactmentTitleDeleteStatus()->id;
        $this->save();
    }
}
