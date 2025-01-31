<?php

namespace Modules\LMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Database\factories\ContentTypeFactory;

class ContentType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    protected $table = 'content_type';

    protected $fillable = [
        'name',
        'id'
    ];

    public function contents()
    {
        return $this->hasMany(Content::class, 'content_type_id', 'id');
    }


}
