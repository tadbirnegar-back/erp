<?php

namespace Modules\ACMS\app\Http\Enums;

enum BudgetTimelineStatusEnum: string
{
    case PROPOSED = 'پیشنهاد به شورا روستا';
    case PENDING_FOR_APPROVAL = 'در انتظار تایید شورا';
    case PENDING_FOR_HEYAAT_APPROVAL = 'در انتظار تایید';
    case FINALIZED = 'تصویب بودجه';
    case CANCELED = 'لغو شده';

    public static function generateTimeline($budget)
    {
        // Define the statuses with default values
        $statuses = [
            BudgetStatusEnum::PENDING_FOR_APPROVAL->value => [
                'status_name' => self::PROPOSED->value,
                'class_name' => 'mute',
                'person' => null,
                'humanReadableCreateDate' => '-',
            ],
            BudgetStatusEnum::PENDING_FOR_HEYAAT_APPROVAL->value => [
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
            BudgetStatusEnum::CANCELED->value => [
                'status_name' => self::CANCELED->value,
                'class_name' => 'mute',
                'person' => null,
                'humanReadableCreateDate' => '-',
            ],
        ];

        // Get the current statuses of the budget
        $currentStatuses = $budget->statuses->pluck('name')->toArray();

        // Check if the budget is canceled
        $isCanceled = in_array(BudgetStatusEnum::CANCELED->value, $currentStatuses);

        // Remove FINALIZED if the budget is canceled
        if ($isCanceled) {
            unset($statuses[BudgetStatusEnum::FINALIZED->value]);
        } else {
            // Remove CANCELED if the budget is not canceled
            unset($statuses[BudgetStatusEnum::CANCELED->value]);
        }

        // Mark statuses based on the current progress
        foreach ($statuses as $statusKey => &$status) {
            if (in_array($statusKey, $currentStatuses)) {
                $currentStatus = $budget->statuses->firstWhere('name', $statusKey);
                $status['class_name'] = $statusKey === BudgetStatusEnum::CANCELED->value ? 'danger' : 'success';
                $status['person'] = $currentStatus->pivot->person->display_name ?? null;
                $status['humanReadableCreateDate'] = DateformatToHumanReadableJalali(
                    convertGregorianToJalali($currentStatus->status_create_date),
                    false
                );
                $status['file'] = is_null($currentStatus->pivot?->file) ? null : [
                    'slug' => $currentStatus->pivot->file->slug,
                    'size' => $currentStatus->pivot->file->size,
                    'name' => $currentStatus->pivot->file->name,
                ];

                $status['description'] = $currentStatus->pivot?->description;
            }
        }

        // Determine the current and future statuses
        if (!$isCanceled) {
            $keys = array_keys($statuses);
            $lastGreenIndex = null;

            foreach ($keys as $index => $statusKey) {
                if (in_array($statusKey, $currentStatuses)) {
                    $lastGreenIndex = $index;
                }
            }

            if ($lastGreenIndex !== null) {
                // Set the current status as primary
                $statuses[$keys[$lastGreenIndex]]['class_name'] = 'primary';

                // Set upcoming statuses to mute
                for ($i = $lastGreenIndex + 1; $i < count($keys); $i++) {
                    $statuses[$keys[$i]]['class_name'] = 'mute';
                }
            }
        }

        return collect($statuses)->values();
    }


}
