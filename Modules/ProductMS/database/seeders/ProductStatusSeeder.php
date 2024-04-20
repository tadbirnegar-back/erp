<?php

namespace Modules\ProductMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\ProductMS\app\Models\Product;

class ProductStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/productStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => Product::class,
            ]);
        }
        // $this->call([]);
    }
}
