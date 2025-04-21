<?php

namespace Modules\PFM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Modules\ACMS\app\Http\Enums\CircularStatusEnum;
use Modules\PFM\app\Http\Enums\BookletStatusEnum;

class ShowBookletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $circular = $this->resource['circular_data'];
        $booklet = $this->resource['booklet_data'];
        $declined = $this->resource['declined'];
        $timeLine = $this->resource['timeLine'];
        return [
            'circular' => [
                'id' => $circular['id'],
                'name' => $circular['circular_name'],
                'description' => $circular['circular_description'],
                'fiscal_year_name' => $circular['fiscal_year_name'],
                'status' => [
                    'name' => $circular['status_name'],
                    'class_name' => $circular['status_class'],
                    'created_date' => $circular['status_name'] == CircularStatusEnum::APPROVED ? $circular['status_created_date'] : null,
                ],
                'file' => [
                    "file_slug" => url($circular['file_slug']),
                    "size" => Number::fileSize($circular['file_size'], 2, 3),
                    'type' => $circular['extension_name'],
                ],
            ],
            'booklet' => [
                'id' => $booklet['id'],
                'status' => [
                    'name' => $booklet['status_name'] == BookletStatusEnum::DAR_ENTEZAR_SABTE_MAGHADIR->value ? 'در انتظار ثبت مقادیر' : $booklet['status_name'],
                    'class_name' => $booklet['status_class'],
                ],
                'dealing_values' => [
                    'commercial' => $booklet['p3'],
                    'administrative' => $booklet['p2'],
                    'residential' => $booklet['p1'],
                ]
            ],
            'levies' => $circular['levies'],
            'declined' => $declined,
            'timeLine' => $timeLine,


        ];
    }
}
