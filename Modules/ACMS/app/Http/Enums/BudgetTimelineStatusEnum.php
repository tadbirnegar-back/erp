<?php

namespace Modules\ACMS\app\Http\Enums;

enum BudgetTimelineStatusEnum: string
{
    case PROPOSED = 'ابلاغ شده';
    case PENDING_FOR_APPROVAL = 'پیشنهاد بودجه';
    case FINALIZED = 'تصویب بودجه';

    public static function generateTimeline($budget)
    {
        // Define the statuses with default values
        $statuses = [
            BudgetStatusEnum::PROPOSED->value => [
                'status_name' => self::PROPOSED->value,
                'class_name' => 'mute',
                'person' => null,
                'humanReadableCreateDate' => '-',

            ],
            BudgetStatusEnum::PENDING_FOR_APPROVAL->value => [
                'status_name' => self::PENDING_FOR_APPROVAL->value,
                'class_name' => 'mute',
                'person' => null,
                'humanReadableCreateDate' => '-',

            ],
            BudgetStatusEnum::FINALIZED->value => [
                'status_name' => self::FINALIZED->value,
                'class_name' => 'mute',
                'person' => null,
                'humanReadableCreateDate' => '-',

            ],
        ];

        // Get the current statuses of the budget
        $currentStatuses = $budget->statuses->pluck('name')->toArray();

        // Mark statuses as green for those the budget has passed through
        foreach ($currentStatuses as $status) {
            if (array_key_exists($status, $statuses)) {
                $currentStatus = $budget->statuses->firstWhere('name', $status);
                $statuses[$status]['class_name'] = 'success';
                $statuses[$status]['person'] = $currentStatus->pivot->person->display_name;
                $statuses[$status]['humanReadableCreateDate'] = DateformatToHumanReadableJalali(convertGregorianToJalali($currentStatus->status_create_date), false);
            }
        }

        // Find the next status to mark as primary
        $keys = array_keys($statuses);
        $lastGreenIndex = null;

        foreach ($keys as $index => $statusKey) {
            if (in_array($statusKey, $currentStatuses)) {
                $lastGreenIndex = $index;
            }
        }

        // Set the next status to primary, if applicable
        if ($lastGreenIndex !== null && isset($keys[$lastGreenIndex + 1])) {
            $statuses[$keys[$lastGreenIndex + 1]]['class_name'] = 'primary';
        }

        return collect($statuses)->values();
    }

}
