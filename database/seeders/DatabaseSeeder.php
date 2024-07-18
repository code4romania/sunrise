<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Organization;
use App\Models\Service;
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
            ->admin()
            ->create();

        Country::factory()
            ->count(195)
            ->create();

        Service::factory()
            ->count(20)
            ->create();

        Organization::factory()
            ->count(10)
            ->withUsers()
            ->withBeneficiaries()
            ->withCommunityProfile()
            ->withInterventions()
            ->create();
    }
}
