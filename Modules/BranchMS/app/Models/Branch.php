<?php

namespace Modules\BranchMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AddressMS\app\Models\Address;
use Modules\BranchMS\Database\factories\BranchFactory;
use Modules\StatusMS\app\Models\Status;

class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public $timestamps = false;
    protected static function newFactory(): BranchFactory
    {
        //return BranchFactory::new();
    }

    public function departments()
    {
        return $this->hasMany(Department::class,'branch_id');
    }

    public function sections()
    {
        return $this->hasManyThrough(Section::class, Department::class);
    }
    public function statuses()
    {
        return $this->belongsToMany(Status::class);
    }
    public function status()
    {
        return $this->belongsToMany(Status::class)->latest('create_date')->take(1);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public static function GetAllStatuses(): \Illuminate\Database\Eloquent\Collection
    {
        return Status::all()->where('model', '=', self::class);
    }
}
