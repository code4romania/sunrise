<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Benefit;
use App\Models\Country;
use App\Models\Institution;
use App\Models\Result;
use App\Models\Role;
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
            ->withUserStatus()
            ->create();

        Country::factory()
            ->count(195)
            ->create();

        Service::factory()
            ->count(20)
            ->create();

        Role::factory()
            ->count(16)
            ->create();

        Benefit::factory()
            ->count(20)
            ->create();

        Result::factory()
            ->count(20)
            ->create();

        Institution::factory()
            ->count(2)
            ->withOrganization()
            ->create();
    }
}
