<?php


namespace Modules\LMS\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\LMS\app\Models\Question;

class OunitCatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $OptionStatusesData = json_decode(file_get_contents(realpath(__DIR__ . '/OunitCatsSeeder.json')), true);

        foreach ($OptionStatusesData as $optionStatus) {
            DB::table('ounit_cats')->updateOrInsert([
                'name' => $optionStatus['name'],
            ], [
                'name' => $optionStatus['name'],
            ]);
        }
    }
}
