<?php

namespace Modules\ACC\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TarazLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $untilNowCredit = $this->opening_credit + $this->period_credit;
        $untilNowDebt = $this->opening_debt + $this->period_debt;
        $remaining = $untilNowCredit - $untilNowDebt;
        if ($remaining < 0) {
            $remainingCredit = 0;
            $remainingDebt = abs($remaining);
        } elseif ($remaining > 0) {
            $remainingCredit = abs($remaining);
            $remainingDebt = 0;
        } else {
            $remainingCredit = 0;
            $remainingDebt = 0;
        }
        return [
            'name' => $this->name.($this->status_id == 155 ? ' (غیرفعال) ' : ''),
            'chainCode' => $this->chain_code,
            'openingCredit' => $this->opening_credit,
            'periodCredit' => $this->period_credit,
            'untilNowCredit' => $untilNowCredit,
            'remainingCredit' => $remainingCredit,
            'openingDebt' => $this->opening_debt,
            'periodDebt' => $this->period_debt,
            'untilNowDebt' => $untilNowDebt,
            'remainingDebt' => $remainingDebt,
        ];
    }
}
