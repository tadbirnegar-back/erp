<?php

namespace Modules\AAA\database\seeders;

use Illuminate\Database\Seeder;
use Modules\AAA\app\Models\Role;

class RoleStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userStatusesData = json_decode(file_get_contents(realpath(__DIR__.'/roleStatus.json')), true);

        foreach ($userStatusesData as $userStatus) {
            \DB::table('statuses')->insertGetId([
                'name' => $userStatus['name'],
                'model' => Role::class,
            ]);
        }
        // $this->call([]);
    }
}
