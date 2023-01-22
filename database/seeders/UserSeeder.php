<?php

namespace Database\Seeders;

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
        $user = User::create([
            'name' => 'Muhammad Halim Dirgantara',
            'email' => 'halimdirgantara@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '6281251413425',
            'status' => 'active',
        ]);
        $user->AssignRole('Super Admin'); 
    }
}
