<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 * 
 * Main seeder that calls all other seeders.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed successfully!');
    }
}
