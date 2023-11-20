<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Mail;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        Mail::fake();

        User::factory(['email' => 'admin@example.com'])
            ->create();

        User::factory()
            ->count(10)
            ->create();
    }
}
