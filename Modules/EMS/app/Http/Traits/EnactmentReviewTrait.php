<?php

namespace Modules\EMS\app\Http\Traits;

use Modules\EMS\app\Http\Enums\EnactmentReviewEnum;
use Modules\EMS\app\Models\EnactmentReview;

trait EnactmentReviewTrait
{


    public function reviewInconsistencyStatus()
    {
        return EnactmentReview::GetAllStatuses()->firstWhere('name', EnactmentReviewEnum::INCONSISTENCY->value);
    }

    public function reviewNoInconsistencyStatus()
    {
        return EnactmentReview::GetAllStatuses()->firstWhere('name', EnactmentReviewEnum::NO_INCONSISTENCY->value);
    }

    public function reviewUnknownStatus()
    {
        return EnactmentReview::GetAllStatuses()->firstWhere('name', EnactmentReviewEnum::UNKNOWN->value);
    }

    public function reviewNoSystemInconsistencyStatus()
    {
        return EnactmentReview::GetAllStatuses()->firstWhere('name', EnactmentReviewEnum::SYSTEM_NO_INCONSISTENCY->value);
    }
}
