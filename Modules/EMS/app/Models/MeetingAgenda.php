<?php

namespace Modules\EMS\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\EMS\Database\factories\MeetingAgendaFactory;

class MeetingAgenda extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    //protected $fillable = [];

    public $timestamps = false;

    public function attachments(): MorphToMany
    {
        return $this->morphToMany(Attachmentable::class, 'attachmentable');
    }

}
