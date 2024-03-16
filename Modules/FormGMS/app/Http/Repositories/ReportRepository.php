<?php

namespace Modules\FormGMS\app\Http\Repositories;

use Mockery\Exception;
use Modules\FormGMS\app\Models\Report;
use Modules\FormGMS\app\Models\ReportRecord;

class ReportRepository
{
//    protected Report $report;
//    protected ReportRecord $reportRecord;
//
//    /**
//     * @param Report $report
//     * @param ReportRecord $reportRecord
//     */
//    public function __construct(Report $report, ReportRecord $reportRecord)
//    {
//        Report = $report;
//        ReportRecord = $reportRecord;
//    }


    public function reportStore(array $data)
    {
        try {
            \DB::beginTransaction();
            /**
             * @var Report $report
             */
            $report = new Report();
            $report->form_id = $data['formID'];
            $report->creator_id = $data['userID'];
            $report->save();
            \DB::commit();
            return $report;

        } catch (Exception $e) {
            \DB::rollBack();
            return $e;

        }
    }
    public function reportRecordstore(array $data)
    {
        try {
            \DB::beginTransaction();
            /**
             * @var ReportRecord $report
             */
            $report = new ReportRecord();
            $report->field_id = $data['fieldID'];
            $report->report_id = $data['reportID'];
            $report->value = $data['value'];
            $report->save();
            \DB::commit();

            return $report;
        }catch (\Exception $e){
            \DB::rollBack();
            return $e;
        }
    }


}
