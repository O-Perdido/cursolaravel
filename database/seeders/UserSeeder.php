<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Vinicius Suporte',
            'email' => 'suporte@vinicius.com',
            'password' => Hash::make('Admin.123'),
            'nivel' => 'admin',
            'senha' => Crypt::encryptString('Admin.123'),
        ]);
    }
}
