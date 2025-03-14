<?php

namespace Modules\BNK\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\BNK\Database\factories\BankFactory;
use Modules\FileMS\app\Models\File;

class Bank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'logo_id',
    ];

    public $timestamps = false;
    protected $table = 'bnk_banks';

    public function logo(): BelongsTo
    {
        return $this->belongsTo(File::class, 'logo_id');
    }

    public function bankBranches(): HasMany
    {
        return $this->hasMany(BankBranch::class, 'bank_id');
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

}
