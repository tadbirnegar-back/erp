<?php

namespace Modules\ACC\app\Http\Traits;

use Modules\ACC\app\Http\Enums\DocumentStatusEnum;
use Modules\ACC\app\Models\Document;
use Modules\ACC\app\Models\DocumentStatus;
use Modules\StatusMS\app\Models\Status;

trait DocumentTrait
{
    public function storeDocument(array $data)
    {
        $data = $this->documentDataPreparation($data);
        $document = Document::create($data->toArray()[0]);

        return $document;

    }

    public function updateDocument(Document $document, array $data)
    {
        $document->description = $data['description'];
        $document->document_number = $data['documentNumber'];
        $document->document_date = $data['documentDate'];
        $document->save();
        return $document;

    }

    public function attachStatusToDocument(Document $document, Status $status, int $userID)
    {
        $documentStatus = DocumentStatus::create([
            'document_id' => $document->id,
            'status_id' => $status->id,
            'creator_id' => $userID,
        ]);

        return $documentStatus;
    }

    public function draftDocumentStatus()
    {
        return Document::GetAllStatuses()->where('name', DocumentStatusEnum::DRAFT->value)->first();
    }

    public function confirmedDocumentStatus()
    {
        return Document::GetAllStatuses()->where('name', DocumentStatusEnum::CONFIRMED->value)->first();
    }

    public function deleteDocumentStatus()
    {
        return Document::GetAllStatuses()->where('name', DocumentStatusEnum::DELETED->value)->first();
    }

    public function documentDataPreparation(array $data)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $data = collect($data)->map(function ($item) {

            return [
                'document_number' => convertToDbFriendly($item['documentNumber']) ?? null,
                'document_date' => $item['documentDate'] ?? null,
                'fiscal_year_id' => $item['fiscalYearID'],
                'ounit_id' => $item['ounitID'],
                'creator_id' => $item['userID'],
                'document_type_id' => $item['documentTypeID'] ?? 1,
                'ounit_head_id' => $item['ounitHeadID'] ?? null,
                'read_only' => $item['readOnly'] ?? false,
                'description' => isset($item['description']) ? convertToDbFriendly($item['description']) : null,
            ];
        });

        return $data;
    }

}
