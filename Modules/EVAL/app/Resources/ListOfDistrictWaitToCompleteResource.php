<?php

namespace Modules\EVAL\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListOfDistrictWaitToCompleteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'data' => $this->resource->map(function ($item) {
                return [
                    'id' => $item['id'], // دسترسی به ویژگی‌ها به صورت شیء
                    'title' => $item['title'],
                    'deadline' => $item['deadline'],
                    'status' => $item['status'],
                    'status_class' => $item['status_class'],
                    'ounit_name' => $item['ounit_name'],
                ];
            }),
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $this->currentPage(),
                'from' => $this->firstItem(),
                'last_page' => $this->lastPage(),
                'links' => collect(range(1, $this->lastPage()))->map(function ($page) {
                    return [
                        'url' => $this->url($page),
                        'label' => (string) $page,
                        'active' => $page === $this->currentPage(),
                    ];
                })->prepend([
                    'url' => null,
                    'label' => "&laquo; قبلی",
                    'active' => false,
                ])->push([
                    'url' => $this->nextPageUrl(),
                    'label' => "بعدی &raquo;",
                    'active' => false,
                ])->toArray(),
                'path' => $this->path(),
                'per_page' => $this->perPage(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
            ],
        ];
    }

}
