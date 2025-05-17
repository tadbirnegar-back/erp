<?php

namespace Modules\ODOC\app\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\AAA\app\Http\Traits\OtpTrait;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\ODOC\app\Http\Enums\DocumentStatusEnum;
use Modules\ODOC\app\Http\Enums\OdocDocumentComponentsTypeEnum;
use Modules\ODOC\app\Http\Enums\TypeOfOdocDocumentsEnum;
use Modules\ODOC\app\Models\Approvers;
use Modules\ODOC\app\Models\Document;
use Modules\ODOC\app\Models\DocumentStatus;
use Modules\PersonMS\app\Http\Traits\SignaturesTrait;
use Modules\PersonMS\app\Models\Person;

trait OdocDocumentTrait
{
    use OdocApproversTrait , OtpTrait , SignaturesTrait;
    public function storeOdocDocument($data, $userID)
    {
        $document = null;

        switch ($data['component_to_render']) {
            case OdocDocumentComponentsTypeEnum::BONYADE_MASKAN->value:
                $document = $this->bonyade_maskan_pdf($data, $userID);
        }


        if ($document != null) {
            $this->setApprovers($document->id, $data['approvers']);
            $this->setStatus($document->id, $data['status_id'] , $data['status_description'] , $userID);
        }
    }

    private function bonyade_maskan_pdf($data, $user)
    {

        $lastFiscalYear = FiscalYear::orderBy('name', 'desc')->first();

        $moduleData = config('moduleCodes.modules.BDM');
        $moduleCode = $moduleData['code'];
        $model = $moduleData['models']["Modules\\BDM\\app\\Models\\Form"];


        $dataArray = [
            'display_name' => $data['title'],
            'national_code' => $data['national_code'],
            'dossier_id' => $data['id'],
            'tracking_code' => $data['tracking_code'],
            'created_date' => $data['created_date'],
            'bdm_type_name' => $data['bdm_type_name'],
            'father_name' => $data['father_name'],
            'gender_name' => $data['gender_name'],
            'birth_location' => $data['birth_location'],
            'bc_code' => $data['bc_code'],
            'city_name' => $data['city_name'],
            'district_name' => $data['district_name'],
            'village_id' => $data['village_id'],
            'village_name' => $data['village_name'],
            'building_number' => $data['building_number'],
            'area' => $data['area'],
            'address' => $data['address'],
            'east' => $data['east'],
            'north' => $data['north'],
            'west' => $data['west'],
            'south' => $data['south'],
            'model_id' => $data['model_id'],
            'version' => $data['version'],
            'status_id' => $data['status_id'],
            'model' => $data['model'],
        ];



        $data['serialize_number'] =
            "\u{202A}" . // LRE (forces LTR)
            $lastFiscalYear->name .
            TypeOfOdocDocumentsEnum::DAKHELI->value .
            $moduleCode .
            $model .
            $data['model_id'] .
            "\u{202C}";  // PDF (ends formatting)
        $data['crypted_data'] = encrypt_json($dataArray);
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
                'ounit_id' => $dossierDocument['ounit_id'],
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
            'ounit_id' => $data['ounit_id'],
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

    public function fetchAllRelatedDocuments($data , $user , $perPage , $pageNum)
    {
        $query = Document::join('odoc_approvers', 'odoc_approvers.document_id', '=', 'odoc_documents.id')
            ->join('organization_units', 'organization_units.id', '=', 'odoc_documents.ounit_id')
            ->select([
                'odoc_documents.id as document_id',
                'odoc_documents.title as title',
                'odoc_documents.serial_number as serial_number',
                'organization_units.name as ounit_name',
                'odoc_documents.model as model',
                'odoc_documents.created_date as created_date',
            ])
            ->where('odoc_approvers.person_id', $user->person_id)
            ->where('odoc_approvers.status_id', $this->PendingApproversStatus()->id)
            ->when(isset($data['title']), function ($query) use ($data) {
                $query->where('odoc_documents.title', 'like', '%' . $data['title'] . '%');
            })
            ->paginate($perPage, ['*'], 'page', $pageNum);
        return $query;

    }

    public function documentApproval($id , $data)
    {
        $isOtpOk = $this->verifyOtpByMobileMultiAccess($data['mobile'] , $data['otp']);
        if($isOtpOk){
            $approver = Approvers::where('document_id', $id)
                ->where('person_id' , $data['personID'])->first();
            if($approver){
                $personSignature = Person::join('signatures' , 'signatures.person_id' , '=' , 'persons.id')
                    ->select('signatures.id as signature_id')
                    ->where('signatures.status_id' , $this->ActiveSignatureStatus()->id)
                    ->find($data['personID']);

                if(!$personSignature){
                    return ['message' => 'شما امضای دیجیتال خود را تایید نکرده اید'  , 'status' => 202];
                }


                $approver->status_id = $this->ApprovedApproversStatus()->id;
                $approver->signed_date = now();
                $approver->token = null;
                $approver->signature_id = $personSignature->signature_id;
                $approver->save();
                return ['message' => 'امضا با موفقیت تایید شد'  , 'status' => 200];
            }
        }else{
            return ['message' => 'کد تایید وارد شده نادرست میباشد'  , 'status' => 403];
        }
    }



    public function documentDecline($id , $data)
    {
        $isOtpOk = $this->verifyOtpByMobileMultiAccess($data['mobile'] , $data['otp']);
        if($isOtpOk){
            $approver = Approvers::where('document_id', $id)
                ->where('person_id' , $data['personID'])->first();
            if($approver){
                $personSignature = Person::join('signatures' , 'signatures.person_id' , '=' , 'persons.id')
                    ->select('signatures.id as signature_id')
                    ->where('signatures.status_id' , $this->ActiveSignatureStatus()->id)
                    ->find($data['personID']);

                if(!$personSignature){
                    return ['message' => 'شما امضای دیجیتال خود را تایید نکرده اید'  , 'status' => 202];
                }


                $approver->status_id = $this->DeclinedApproversStatus()->id;
                $approver->signed_date = now();
                $approver->token = null;
                $approver->signature_id = $personSignature->signature_id;
                $approver->save();
                return ['message' => 'امضا با موفقیت تایید شد'  , 'status' => 200];
            }
        }else{
            return ['message' => 'کد تایید وارد شده نادرست میباشد'  , 'status' => 403];
        }
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
