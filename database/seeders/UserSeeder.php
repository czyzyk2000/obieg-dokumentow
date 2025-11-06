<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder
 * 
 * Seeds the database with test users for each role.
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'manager_id' => null,
        ]);

        // Manager
        $manager = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::MANAGER,
            'manager_id' => null,
        ]);

        // Finance
        $finance = User::create([
            'name' => 'Anna Nowak',
            'email' => 'finance@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::FINANCE,
            'manager_id' => null,
        ]);

        // Regular users (subordinates of manager)
        User::create([
            'name' => 'Piotr Wiśniewski',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
            'manager_id' => $manager->id,
        ]);

        User::create([
            'name' => 'Maria Lewandowska',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
            'manager_id' => $manager->id,
        ]);

        User::create([
            'name' => 'Tomasz Dąbrowski',
            'email' => 'user3@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
            'manager_id' => $manager->id,
        ]);

        $this->command->info('✅ Created test users:');
        $this->command->info('   Admin: admin@example.com / password');
        $this->command->info('   Manager: manager@example.com / password');
        $this->command->info('   Finance: finance@example.com / password');
        $this->command->info('   User 1: user1@example.com / password');
        $this->command->info('   User 2: user2@example.com / password');
        $this->command->info('   User 3: user3@example.com / password');
    }
}
