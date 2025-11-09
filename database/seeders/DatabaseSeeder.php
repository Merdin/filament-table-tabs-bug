<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Course::factory()
            ->create([
                'name' => 'Course in past',
                'start_at' => now()->subWeek()->toDateString(),
            ]);

        Course::factory()
            ->create([
                'name' => 'Course today',
                'start_at' => now()->toDateString(),
            ]);

        Course::factory()
            ->create([
                'name' => 'Course in future',
                'start_at' => now()->addWeek()->toDateString(),
            ]);
    }
}
