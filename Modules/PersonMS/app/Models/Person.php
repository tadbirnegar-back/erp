<?php

namespace Modules\PersonMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\FileMS\app\Models\File;
use Modules\PersonMS\Database\factories\PersonFactory;
use Modules\StatusMS\app\Models\Status;

class Person extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    protected $table = 'persons';
    protected static function newFactory(): PersonFactory
    {
        //return PersonFactory::new();
    }

    public function personable()
    {
        return $this->morphTo();
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class);
    }

    public function avatar()
    {
        return $this->belongsTo(File::class, 'profile_picture_id');
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
