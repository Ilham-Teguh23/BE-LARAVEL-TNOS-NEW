<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = [
            [
                "name"          => "deka",
                "is_active"     => 1
            ],
            [
                "name"          => "trigger",
                "is_active"     => 1
            ],
            [
                "name"          => "pass",
                "is_active"     => 1
            ],
        ];

        Client::insert($client);
    }
}
