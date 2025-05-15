<?php

namespace Modules\ODOC\app\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\ODOC\app\Http\Enums\DocumentStatusEnum;
use Modules\ODOC\app\Http\Enums\TypeOfOdocDocumentsEnum;
use Modules\ODOC\app\Models\Approvers;
use Modules\ODOC\app\Models\Document;
use Modules\ODOC\app\Models\DocumentStatus;

trait OdocDocemntTrait
{
    use OdocApproversTrait;
    public function storeOdocDocument($data, $userID)
    {
        $document = null;
        switch ($data['component_to_render']) {
            case 'dossier_form_1_2':
                $data['model'] = BuildingDossier::class;
                $data['version'] = '1';
                $document = $this->dossier_form_1_2($data, $userID);
        }


        if ($document != null) {
            $this->setApprovers($document->id, $data['approvers']);
            $this->setStatus($document->id, $data['status_id'] , $data['status_description'] , $userID);
        }
    }

    private function dossier_form_1_2($data, $user)
    {

        $lastFiscalYear = FiscalYear::orderBy('name', 'desc')->first();

        $moduleData = config('moduleCodes.modules.BDM');
        $moduleCode = $moduleData['code'];
        $model = $moduleData['models']["Modules\\FormGMS\\app\\Models\\Form"];

        $buildingDossier = BuildingDossier::find($data['model_id']);

        $key = env('SECRET_KEY_FOR_ENCRYPT');
        $iv = openssl_random_pseudo_bytes(16);

        $encrypted = openssl_encrypt(json_encode($buildingDossier), 'AES-128-CBC', $key, 0, $iv);
        $cryptedData = base64_encode($iv . $encrypted);

        $data['serialize_number'] = $lastFiscalYear->name . ' ' . TypeOfOdocDocumentsEnum::DAKHELI->value . ' ' . $moduleCode . $model . $data['model_id'];
        $data['crypted_data'] = $cryptedData;
        $dossierDocument = $this->oducDocumentDataPrepration($data, $user);
        return Document::updateOrCreate(
            [
                'model' => $dossierDocument['model'],
                'model_id' => $dossierDocument['model_id'],
                'version' => $dossierDocument['version'],
            ],
            [
                'component_to_render' => $dossierDocument['component_to_render'],
                'data' => $dossierDocument['data'],
                'model' => $dossierDocument['model'],
                'model_id' => $dossierDocument['model_id'],
                'serial_number' => $dossierDocument['serial_number'],
                'title' => $dossierDocument['title'],
                'created_date' => $dossierDocument['created_date'],
                'creator_id' => $dossierDocument['creator_id'],
                'version' => $dossierDocument['version'],
            ]);
    }

    private function oducDocumentDataPrepration($data, $userID)
    {
        return [
            'component_to_render' => $data['component_to_render'],
            'data' => $data['crypted_data'],
            'model' => $data['model'],
            'model_id' => $data['model_id'],
            'serial_number' => $data['serialize_number'],
            'title' => $data['title'],
            'created_date' => Carbon::now(),
            'creator_id' => $userID,
            'version' => $data['version'],
        ];

    }

    public function setStatus($documentID, $statusID , $status_description , $creator_id)
    {
        DocumentStatus::create([
            'status_id' => $statusID,
            'odoc_document_id' => $documentID,
            'description' => $status_description,
            'creator_id' => $creator_id,
            'created_date' => Carbon::now(),
        ]);
    }


    public function CompletedOdocDocumentStatus()
    {
        return Cache::rememberForever('active_odoc_document_status', function () {
            return Document::GetAllStatuses()
                ->firstWhere('name', DocumentStatusEnum::COMPLETED->value);
        });
    }

    public function PendingOdocDocumentStatus()
    {
        return Cache::rememberForever('pending_odoc_document_status', function () {
            return Document::GetAllStatuses()
                ->firstWhere('name', DocumentStatusEnum::PENDING->value);
        });
    }

    public function DeclinedOdocDocumentStatus()
    {
        return Cache::rememberForever('declined_odoc_document_status', function () {
            return Document::GetAllStatuses()
                ->firstWhere('name', DocumentStatusEnum::DECLINED->value);
        });
    }

}
