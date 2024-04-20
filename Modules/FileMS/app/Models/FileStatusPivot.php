<?php

namespace Modules\FileMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\FileMS\Database\factories\FileStatusPivotFactory;

class FileStatusPivot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    protected $table = 'file_status';
    public $timestamps = false;
    protected static function newFactory(): FileStatusPivotFactory
    {
        //return FileStatusPivotFactory::new();
    }
}
