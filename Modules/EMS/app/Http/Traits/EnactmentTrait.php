<?php

namespace Modules\EMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\EMS\app\Http\Enums\EnactmentStatusEnum;
use Modules\EMS\app\Models\Attachmentable;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\EnactmentStatus;
use Modules\EMS\app\Models\Meeting;

trait EnactmentTrait
{

    public function indexPendingForSecretaryStatusEnactment(array $data)
    {
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $query = Enactment::whereHas('status', function ($query) {
            $query->join('enactment_status as rss', 'enactments.id', '=', 'rss.enactment_id')
                ->join('statuses as s', 'rss.status_id', '=', 's.id')
                ->where('s.name', EnactmentStatusEnum::PENDING_SECRETARY_REVIEW->value)
                ->where('rss.create_date', function ($subQuery) {
                    $subQuery->selectRaw('MAX(create_date)')
                        ->from('enactment_status as sub_rss')
                        ->whereColumn('sub_rss.enactment_id', 'rss.enactment_id');
                });
        });

        if (!empty($data['title'])) {
            $query->where(function ($query) use ($data) {
                $query->whereRaw('MATCH(custom_title) AGAINST(? IN BOOLEAN MODE)', [$data['title']])
                    ->orWhereHas('title', function ($query) use ($data) {
                        $query->whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$data['title']]);
                    });
            });
        }

        return $query->with(['status', 'meeting', 'reviewStatuses', 'title'])
            ->paginate($perPage, ['*'], 'page', $pageNum);
    }

    public function indexPendingForHeyaatStatusEnactment(array $data)
    {
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $query = Enactment::whereHas('status', function ($query) {
            $query->join('enactment_status as rss', 'enactments.id', '=', 'rss.enactment_id')
                ->join('statuses as s', 'rss.status_id', '=', 's.id')
                ->where('s.name', EnactmentStatusEnum::PENDING_BOARD_REVIEW->value)
                ->where('rss.create_date', function ($subQuery) {
                    $subQuery->selectRaw('MAX(create_date)')
                        ->from('enactment_status as sub_rss')
                        ->whereColumn('sub_rss.enactment_id', 'rss.enactment_id');
                });
        });

        if (!empty($data['title'])) {
            $query->where(function ($query) use ($data) {
                $query->whereRaw('MATCH(custom_title) AGAINST(? IN BOOLEAN MODE)', [$data['title']])
                    ->orWhereHas('title', function ($query) use ($data) {
                        $query->whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$data['title']]);
                    });
            });
        }

        return $query->with(['status', 'meeting', 'reviewStatuses', 'title'])
            ->paginate($perPage, ['*'], 'page', $pageNum);
    }

    public function indexPendingForArchiveStatusEnactment(array $data)
    {
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;
        $statuses = $data['statusID'] ?? null;
        $searchTerm = $data['name'] ?? null;

        $query = Enactment::whereHas('status', function ($query) use ($statuses) {
            $query->join('enactment_status as rss', 'enactments.id', '=', 'rss.enactment_id')
                ->join('statuses as s', 'rss.status_id', '=', 's.id')
                ->when($statuses, function ($query) use ($statuses) {
                    $query->where('rss.status_id', $statuses);
                })
                ->where('rss.create_date', function ($subQuery) {
                    $subQuery->selectRaw('MAX(create_date)')
                        ->from('enactment_status as sub_rss')
                        ->whereColumn('sub_rss.enactment_id', 'rss.enactment_id');
                });
        });

        $query->when($searchTerm, function ($query) use ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->whereRaw('MATCH(custom_title) AGAINST(? IN BOOLEAN MODE)', [$searchTerm])
                    ->orWhereHas('title', function ($query) use ($searchTerm) {
                        $query->whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$searchTerm]);
                    });
            });
        });


        return $query->with(['status', 'meeting', 'reviewStatuses', 'title'])
            ->paginate($perPage, ['*'], 'page', $pageNum);
    }

    public function storeEnactment(array|Collection $data, Meeting $meeting)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $preparedData = $this->prepareEnactmentData($data, $meeting);
        $result = Enactment::create($preparedData->toArray()[0]);
        $status = $this->enactmentPendingSecretaryStatus();
        $enactmentStatus = new EnactmentStatus();
        $enactmentStatus->enactment_id = $result->id;
        $enactmentStatus->status_id = $status->id;
        $enactmentStatus->operator_id = $data[0]['creatorID'];
        $enactmentStatus->save();
//        $result->statuses()->attach($status->id);

        return $result;
    }

    private function prepareEnactmentData(array|Collection $data, Meeting $meeting)
    {
        if (is_array($data)) {
            $data = collect($data);
        }

        $data = $data->map(function ($item) use ($meeting) {
            return [
                'custom_title' => $item['customTitle'] ?? null,
                'description' => $item['description'] ?? null,
                'rejection_reason' => $item['rejectionReason'] ?? null,
                'auto_serial' => $item['autoSerial'] ?? null,
                'serial' => $item['serial'] ?? null,
                'title_id' => $item['titleID'] ?? null,
                'creator_id' => $item['creatorID'],
                'meeting_id' => $meeting->id ?? null,
                'rejection_file_id' => $item['rejectionFileID'] ?? null,
                'create_date' => now(),
            ];
        });

        return $data;
    }

    public function readEnactment(int $id)
    {
        return Enactment::findOrFail($id);
    }

    public function updateEnactment(array $data, Enactment $enactment, Meeting $meeting)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $preparedData = $this->prepareEnactmentData($data, $meeting)->toArray()[0];
        $enactment->update($preparedData);
        return $enactment;
    }

    public function deleteEnactment(int $id)
    {
        $enactment = Enactment::findOrFail($id);
        $enactment->delete();
        return $enactment;
    }

    public function attachFiles(Enactment $enactment, array $files)
    {
        $attachments = collect($files)->map(function ($file) use ($enactment) {
            return [
                'attachment_id' => $file['fileID'],
                'title' => $file['title'] ?? null,
                'attachmentable_id' => $enactment->id,
                'attachmentable_type' => Enactment::class,
            ];
        })->toArray();

        Attachmentable::insert($attachments);
    }

    public function enactmentPendingSecretaryStatus()
    {
        return Enactment::GetAllStatuses()->firstWhere('name', EnactmentStatusEnum::PENDING_SECRETARY_REVIEW->value);
    }
}
