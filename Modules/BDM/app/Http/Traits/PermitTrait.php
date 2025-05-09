<?php

namespace Modules\BDM\app\Http\Traits;


use Modules\BDM\app\Http\Enums\PermitStatusesEnum;
use Modules\BDM\app\Models\PermitStatus;

trait PermitTrait
{
    public function firstStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::first->value);
    }

    public function secondStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::second->value);
    }

    public function thirdStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::third->value);
    }

    public function fourthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::fourth->value);
    }

    public function fifthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::fifth->value);
    }

    public function sixthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::sixth->value);
    }

    public function seventhStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::seventh->value);
    }

    public function eighthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::eighth->value);
    }

    public function ninthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::ninth->value);
    }

    public function tenthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::tenth->value);
    }

    public function eleventhStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::eleventh->value);
    }

    public function twelfthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::twelfth->value);
    }

    public function thirteenthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::thirteenth->value);
    }

    public function fourteenthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::fourteenth->value);
    }

    public function fifteenthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::fifteenth->value);
    }

    public function sixteenthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::sixteenth->value);
    }

    public function seventeenthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::seventeenth->value);
    }

    public function failedStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::failed->value);
    }

    public function rejectObligationsStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::rejectObligations->value);
    }

    public function eighteenthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::eighteenth->value);
    }

    public function nineteenthStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::nineteenth->value);
    }

    public function twentiethStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::twentieth->value);
    }

    public function twentyfirstStatus()
    {
        return PermitStatus::GetAllStatuses()->firstWhere('name', PermitStatusesEnum::twentyfirst->value);
    }
}
