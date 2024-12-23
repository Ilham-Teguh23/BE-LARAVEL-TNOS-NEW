<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Partnerdeka;



class PartnerdekaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deka = [
            [
                "code"                   => strtoupper(Str::random(8)),
                "name"                   => "TNOS",
                "komisi_value_partner"   =>  "1200000",
                "komisi_percent_partner" =>  "0",
                "deka_percent"           => "90",
                "deka_value"             => "400000",
                "tnos_percent"           =>  "10",
                "tnos_value"             => "200000",
                "value_user"             => "100000",
            ],
            [
                "code"  => strtoupper(Str::random(8)),
                "name"  => "DEKA",
                "komisi_value_partner"   =>  "1200000",
                "komisi_percent_partner" =>  "0",
                "deka_percent"           => "98",
                "deka_value"             => "400000",
                "tnos_percent"           =>  "2",
                "tnos_value"             => "200000",
                "value_user"             => "100000",
            ],
            [
                "code"  => strtoupper(Str::random(8)),
                "name"  => "Partner 1",
                "komisi_value_partner"   =>  "1200000",
                "komisi_percent_partner" =>  "5",
                "deka_percent"           => "90",
                "deka_value"             => "400000",
                "tnos_percent"           =>  "5",
                "tnos_value"             => "200000",
                "value_user"             => "100000",
            ],
            [
                "code"  => strtoupper(Str::random(8)),
                "name"  => "Partner 2",
                "komisi_value_partner"   =>  "1200000",
                "komisi_percent_partner" =>  "5",
                "deka_percent"           => "90",
                "deka_value"             => "400000",
                "tnos_percent"           =>  "5",
                "tnos_value"             => "200000",
                "value_user"             => "100000",
            ],
            [
                "code"  => strtoupper(Str::random(8)),
                "name"  => "Partner 3",
                "komisi_value_partner"   =>  "1200000",
                "komisi_percent_partner" =>  "5",
                "deka_percent"           => "90",
                "deka_value"             => "400000",
                "tnos_percent"           =>  "5",
                "tnos_value"             => "200000",
                "value_user"             => "100000",
            ],
            [
                "code"  => strtoupper(Str::random(8)),
                "name"  => "Partner 4",
                "komisi_value_partner"   =>  "1200000",
                "komisi_percent_partner" =>  "5",
                "deka_percent"           => "90",
                "deka_value"             => "400000",
                "tnos_percent"           =>  "5",
                "tnos_value"             => "200000",
                "value_user"             => "100000",
            ]
        ];
        Partnerdeka::insert($deka);
    }
}
