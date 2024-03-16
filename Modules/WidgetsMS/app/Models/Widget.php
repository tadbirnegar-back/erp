<?php

namespace Modules\WidgetsMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\WidgetsMS\Database\factories\WidgetFactory;

class Widget extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    
    protected static function newFactory(): WidgetFactory
    {
        //return WidgetFactory::new();
    }
}
