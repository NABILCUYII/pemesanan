<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        Role::create([
            'user_id' => $user->id,
            'role' => 'admin'
        ]);

        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        Role::create([
            'user_id' => $user->id,
            'role' => 'user'
        ]);

        $user = User::create([
            'name' => 'Pengguna Baru',
            'email' => 'penggunabaru@example.com',
            'password' => Hash::make('password'),
        ]);

        Role::create([
            'user_id' => $user->id,
            'role' => 'penggunaBARU'
        ]);
    }
} 