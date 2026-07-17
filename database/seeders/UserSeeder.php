<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1 Admin
        User::firstOrCreate(
            ['email' => 'admin@tginvest.com'],
            [
                'name' => 'Admin TG Invest',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '+225 01 00 00 00 00',
                'is_active' => true,
            ]
        );

        // 2 Techniciens
        User::firstOrCreate(
            ['email' => 'technicien1@tginvest.com'],
            [
                'name' => 'Jean Kouassi',
                'password' => Hash::make('password123'),
                'role' => 'technician',
                'phone' => '+225 07 11 11 11 11',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'technicien2@tginvest.com'],
            [
                'name' => 'Marie Diabaté',
                'password' => Hash::make('password123'),
                'role' => 'technician',
                'phone' => '+225 07 22 22 22 22',
                'is_active' => true,
            ]
        );

        // 2 Clients
        User::firstOrCreate(
            ['email' => 'client1@tginvest.com'],
            [
                'name' => 'Paul Yao',
                'password' => Hash::make('password123'),
                'role' => 'client',
                'phone' => '+225 01 33 33 33 33',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'client2@tginvest.com'],
            [
                'name' => 'Sophie Koné',
                'password' => Hash::make('password123'),
                'role' => 'client',
                'phone' => '+225 01 44 44 44 44',
                'is_active' => true,
            ]
        );
    }
}
