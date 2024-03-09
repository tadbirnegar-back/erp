<?php

namespace Modules\Merchandise\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Merchandise\app\Models\MerchandiseProduct;

class MerchandiseStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/merchandiseStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => MerchandiseProduct::class,
            ]);
        }
        // $this->call([]);
    }
}
