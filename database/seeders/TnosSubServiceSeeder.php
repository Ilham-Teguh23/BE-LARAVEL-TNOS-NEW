<?php

namespace Database\Seeders;

use App\Models\TnosSubService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TnosSubServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'tnos_service_id' => 1,
                'name_subservice' => 'Konsultasi Hukum',
            ],
            [
                'id' => 2,
                'tnos_service_id' => 1,
                'name_subservice' => 'Pendampingan Hukum',
            ],
            [
                'id' => 3,
                'tnos_service_id' => 2,
                'name_subservice' => 'Pengamanan perorangan',
            ],
            [
                'id' => 4,
                'tnos_service_id' => 3,
                'name_subservice' => 'Pembuatan Badan CV',
            ],
            [
                'id' => 5,
                'tnos_service_id' => 3,
                'name_subservice' => 'Pembuatan Badan PT',
            ],
            [
                'id' => 6,
                'tnos_service_id' => 2,
                'name_subservice' => 'Pengamanan Korporat',
            ],
        ];

        TnosSubService::insert($data);
    }
}
