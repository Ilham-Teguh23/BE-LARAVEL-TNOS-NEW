<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'name' => 'admin percobaan',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'is_email_verified' => 1,
        ];

        User::create($data);
    }
}
