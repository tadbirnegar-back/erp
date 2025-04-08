<?php

namespace Modules\EMS\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\EMS\app\Http\Enums\EnactmentReviewEnum;
use Modules\EMS\app\Models\EnactmentReview;

trait EnactmentReviewTrait
{


    public function reviewInconsistencyStatus()
    {
        return Cache::rememberForever('enactment_review_inconsistency_status', function () {
            return EnactmentReview::GetAllStatuses()
                ->firstWhere('name', EnactmentReviewEnum::INCONSISTENCY->value);
        });
    }

    public function reviewNoInconsistencyStatus()
    {
        return Cache::rememberForever('enactment_review_no_inconsistency_status', function () {
            return EnactmentReview::GetAllStatuses()
                ->firstWhere('name', EnactmentReviewEnum::NO_INCONSISTENCY->value);
        });
    }

    public function reviewUnknownStatus()
    {
        return Cache::rememberForever('enactment_review_unknown_status', function () {
            return EnactmentReview::GetAllStatuses()
                ->firstWhere('name', EnactmentReviewEnum::UNKNOWN->value);
        });
    }

    public function reviewNoSystemInconsistencyStatus()
    {
        return Cache::rememberForever('enactment_review_no_system_inconsistency_status', function () {
            return EnactmentReview::GetAllStatuses()
                ->firstWhere('name', EnactmentReviewEnum::SYSTEM_NO_INCONSISTENCY->value);
        });
    }
}
