<?php

namespace Modules\PFM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Modules\ACMS\app\Http\Enums\CircularStatusEnum;

class ShowCircularResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $data = $this->resource['data'];
        $bookletsStatus = $this->resource['reportOfPublishedBooklets'];

        return [
            'id' => $data['id'],
            'name' => $data['circular_name'],
            'description' => $data['circular_description'],
            'created_date' => $data['created_date'],
            'fiscal_year_name' => $data['fiscal_year_name'],
            'status' => [
                'name' => $data['status_name'],
                'class_name' => $data['status_class'],
                'created_date' => $data['status_created_date'],
            ],
            'publishedDate' => $data['status_name'] == CircularStatusEnum::APPROVED->value ? convertDateTimeGregorianToJalaliDateTime($data['created_date']) : null,
            'file' => [
                "file_slug" => $data['file_slug'],
                "size" => Number::fileSize($data['file_size'], 2, 3),
                'type' => $data['extension_name'],
            ],
            "booklets_data" => [
                "percentage" => $data['booklets_count'] / $this->resource['countOfVillages'] * 100,
                "count" => $data['booklets_count'],
                "approved" => $bookletsStatus['countOfMosavabStatus'],
                "waiting" => $bookletsStatus['countOfDarEntazarStatus'],
                "suggested" => $bookletsStatus['countOfPishnahadShodeStatus'],
                'remainVillages' => $this->resource['countOfVillages'] - $data['booklets_count'],
                'allVillages' => $this->resource['countOfVillages'],
            ],
            'levies' => $data['levies'],
        ];
    }
}
