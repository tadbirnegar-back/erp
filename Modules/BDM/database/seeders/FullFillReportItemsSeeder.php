<?php
namespace Modules\BDM\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\BDM\app\Models\ReportItem;
use Modules\BDM\app\Models\ReportType;

class FullFillReportItemsSeeder extends Seeder
{
    public function run()
    {
        $reportItems = json_decode(file_get_contents(realpath(__DIR__ . '/FullFillReportItemsSeeder.json')), true);
        foreach ($reportItems as $reportItem) {
            $reportType = ReportType::where('name', $reportItem['type_name'])->first();
            if(!$reportType){
                $reportType = ReportType::create([
                    'name' => $reportItem['type_name'],
                ]);
            }
            foreach ($reportItem['items'] as $item) {
                $reportItem = ReportItem::where('name', $item)->first();
                if(!$reportItem){
                    ReportItem::create([
                        'name' => $item,
                        'report_type_id' => $reportType->id,
                    ]);
                }
            }

        }
    }
}
