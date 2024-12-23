<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Masterprice;

class MasterPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $master_price = [
            // klien dari tnos
            // PT
            [
                'client_id'     => 1,
                'layanan'       => 'PT',
                'price_user'    => '1500000',
                'price_tnos'    => '150000',
                'price_client'  => '1350000',
                'is_active'     => 1,
                'is_client'     => 0,
                'klasifikasi'   => '1',
            ],
            [
                'client_id'     => 1,
                'layanan'       => 'PT',
                'price_user'    => '5500000',
                'price_tnos'    => '550000',
                'price_client'  => '4950000',
                'is_active'     => 1,
                'is_client'     => 0,
                'klasifikasi'   => '2',
            ],
            [
                'client_id'     => 1,
                'layanan'       => 'PT',
                'price_user'    => '6000000',
                'price_tnos'    => '600000',
                'price_client'  => '5400000',
                'is_active'     => 1,
                'is_client'     => 0,
                'klasifikasi'   => '3',
            ],
            [
                'client_id'     => 1,
                'layanan'       => 'PT',
                'price_user'    => '8000000',
                'price_tnos'    => '800000',
                'price_client'  => '7200000',
                'is_active'     => 1,
                'is_client'     => 0,
                'klasifikasi'   => '4',
            ],
            // CV
            [
                'client_id'     => 1,
                'layanan'       => 'CV',
                'price_user'    => '3500000',
                'price_tnos'    => '350000',
                'price_client'  => '3150000',
                'is_active'     => 1,
                'is_client'     => 0,
                'klasifikasi'   => null,
            ],
            // YAYASAN
            [
                'client_id'     => 1,
                'layanan'       => 'YA',
                'price_user'    => '5000000',
                'price_tnos'    => '500000',
                'price_client'  => '4500000',
                'is_active'     => 1,
                'is_client'     => 0,
                'klasifikasi'   => null,
            ],
            // PERKUMPULAN
            [
                'client_id'     => 1,
                'layanan'       => 'PN',
                'price_user'    => '5000000',
                'price_tnos'    => '500000',
                'price_client'  => '4500000',
                'is_active'     => 1,
                'is_client'     => 0,
                'klasifikasi'   => null,
            ],

            // CLIENT DEKA
            // PT
            [
                'client_id'     => 1,
                'layanan'       => 'PT',
                'price_user'    => '1500000',
                'price_tnos'    => '30000',
                'price_client'  => '1470000',
                'is_active'     => 1,
                'is_client'     => 1,
                'klasifikasi'   => '1',
            ],
            [
                'client_id'     => 1,
                'layanan'       => 'PT',
                'price_user'    => '5500000',
                'price_tnos'    => '110000',
                'price_client'  => '5390000',
                'is_active'     => 1,
                'is_client'     => 1,
                'klasifikasi'   => '2',
            ],
            [
                'client_id'     => 1,
                'layanan'       => 'PT',
                'price_user'    => '6000000',
                'price_tnos'    => '120000',
                'price_client'  => '5880000',
                'is_active'     => 1,
                'is_client'     => 1,
                'klasifikasi'   => '3',
            ],
            [
                'client_id'     => 1,
                'layanan'       => 'PT',
                'price_user'    => '8000000',
                'price_tnos'    => '160000',
                'price_client'  => '7840000',
                'is_active'     => 1,
                'is_client'     => 1,
                'klasifikasi'   => '4',
            ],
            // CV
            [
                'client_id'     => 1,
                'layanan'       => 'CV',
                'price_user'    => '3500000',
                'price_tnos'    => '70000',
                'price_client'  => '3430000',
                'is_active'     => 1,
                'is_client'     => 1,
                'klasifikasi'   => null,
            ],
            // YAYASAN
            [
                'client_id'     => 1,
                'layanan'       => 'YA',
                'price_user'    => '5000000',
                'price_tnos'    => '100000',
                'price_client'  => '4900000',
                'is_active'     => 1,
                'is_client'     => 1,
                'klasifikasi'   => null,
            ],
            // PERKUMPULAN
            [
                'client_id'     => 1,
                'layanan'       => 'PN',
                'price_user'    => '5000000',
                'price_tnos'    => '100000',
                'price_client'  => '4900000',
                'is_active'     => 1,
                'is_client'     => 1,
                'klasifikasi'   => null,
            ],
            // AS
            [
                'client_id'     => 1,
                'layanan'       => 'AS',
                'price_user'    => '3500000',
                'price_tnos'    => '3500000',
                'price_client'  => '3500000',
                'is_active'     => 1,
                'is_client'     => 1,
                'klasifikasi'   => null,
            ],
            // SH
            [
                'client_id'     => 1,
                'layanan'       => 'SH',
                'price_user'    => '5000000',
                'price_tnos'    => '5000000',
                'price_client'  => '5000000',
                'is_active'     => 1,
                'is_client'     => 1,
                'klasifikasi'   => null,
            ],
        ];

        Masterprice::insert($master_price);
    }
}
