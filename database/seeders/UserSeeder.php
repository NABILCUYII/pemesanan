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
        // Create existing admin user if not exists
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        if (!$adminUser->role) {
            Role::create([
                'user_id' => $adminUser->id,
                'role' => 'admin'
            ]);
        }

        // Create existing regular user if not exists
        $regularUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
            ]
        );

        if (!$regularUser->role) {
            Role::create([
                'user_id' => $regularUser->id,
                'role' => 'user'
            ]);
        }

        // Create existing pengguna baru if not exists
        $penggunaBaru = User::firstOrCreate(
            ['email' => 'penggunabaru@example.com'],
            [
                'name' => 'Pengguna Baru',
                'password' => Hash::make('password'),
            ]
        );

        if (!$penggunaBaru->role) {
            Role::create([
                'user_id' => $penggunaBaru->id,
                'role' => 'penggunaBARU'
            ]);
        }

        // Create 100 additional users only if they don't exist
        $existingUserCount = User::count();
        $targetUserCount = 103; // 3 existing + 100 new
        
        if ($existingUserCount < $targetUserCount) {
            $usersToCreate = $targetUserCount - $existingUserCount;
            $users = User::factory($usersToCreate)->create();
            
            foreach ($users as $user) {
                // 90% chance of being regular user, 10% chance of being admin
                $role = rand(1, 100) <= 10 ? 'admin' : 'user';
                
                Role::create([
                    'user_id' => $user->id,
                    'role' => $role
                ]);
            }

            $this->command->info("Created {$usersToCreate} additional users with random roles!");
        } else {
            $this->command->info("Users already exist. Total users: {$existingUserCount}");
        }
    }
} 