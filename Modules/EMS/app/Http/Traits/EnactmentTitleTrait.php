<?php

namespace Modules\EMS\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\EMS\app\Http\Enums\EnactmentTitleStatusEnum;
use Modules\EMS\app\Models\EnactmentTitle;

trait EnactmentTitleTrait
{

    public function enactmentTitleIndex()
    {
        $enactmentTitles = EnactmentTitle::all();
        return $enactmentTitles;
    }

    public function enactmentTitleStore(array $data)
    {

        $status = $this->enactmentTitleActiveStatus();
        $enactmentTitle = new EnactmentTitle();
        $enactmentTitle->title = $data['title'];
        $enactmentTitle->status_id = $status->id;

        $enactmentTitle->save();
        return $enactmentTitle;
    }

    public function enactmentTitleUpdate(array $data, EnactmentTitle $enactmentTitle)
    {

        $enactmentTitle->title = $data['title'];

        $enactmentTitle->save();
        return $enactmentTitle;
    }

    public function enactmentTitleDestroy(EnactmentTitle $enactmentTitle)
    {
        $enactmentTitle->delete();
    }

    public function enactmentTitleActiveStatus()
    {
        return Cache::rememberForever('enactment_title_active_status', function () {
            return EnactmentTitle::GetAllStatuses()
                ->where('name', '=', EnactmentTitleStatusEnum::ACTIVE->value)
                ->first();
        });
    }

    public function enactmentTitleDeleteStatus()
    {
        return Cache::rememberForever('enactment_title_deleted_status', function () {
            return EnactmentTitle::GetAllStatuses()
                ->where('name', '=', EnactmentTitleStatusEnum::DELETED->value)
                ->first();
        });
    }

}
