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
                'name' => 'Ananta Ghaisani',
                'email' => 'aylanazefanya@gmail.com',
                'address' => 'Jl. Melati No. 10',
                'phone_number' => '08111111111',
            ],
            [
                'name' => 'Gabriela',
                'email' => 'hanayori2000@gmail.com',
                'address' => 'Jl. Kenanga No. 23',
                'phone_number' => '08222222222',
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
