<?php

namespace Database\Seeders;

use App\Models\TnosService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class TnosServiceSeeder extends Seeder
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
                'name_service' => 'Pengacara',
            ],
            [
                'id' => 2,
                'name_service' => 'Pengamanan',
            ],
            [
                'id' => 3,
                'name_service' => 'Badan Hukum',
            ],
        ];


        TnosService::insert($data);
    }
}
