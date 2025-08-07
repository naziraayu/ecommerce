<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        // Pastikan role 'user' tersedia
        $role = Role::firstOrCreate(
            ['name' => 'user'],
            ['permissions' => ['manage_products', 'manage_categories']] // contoh permissions
        );

        // 5 Data dummy user
        $users = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'address' => 'Jl. Melati No. 10',
                'phone_number' => '08111111111',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'address' => 'Jl. Kenanga No. 23',
                'phone_number' => '08222222222',
            ],
            [
                'name' => 'Rudi Hartono',
                'email' => 'rudi@example.com',
                'address' => 'Jl. Mawar No. 5',
                'phone_number' => '08333333333',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@example.com',
                'address' => 'Jl. Anggrek No. 15',
                'phone_number' => '08444444444',
            ],
            [
                'name' => 'Agus Pratama',
                'email' => 'agus@example.com',
                'address' => 'Jl. Dahlia No. 3',
                'phone_number' => '08555555555',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'), // default password
                    'role' => 'user',
                    'role_id' => $role->id,
                    'address' => $user['address'],
                    'phone_number' => $user['phone_number'],
                ]
            );
        }
    }
}
