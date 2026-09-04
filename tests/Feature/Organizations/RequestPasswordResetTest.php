<?php

declare(strict_types=1);

use App\Filament\Auth\RequestPasswordReset;
use App\Models\User;
use App\Notifications\PasswordReset;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

beforeEach(function () {
    Filament::setCurrentPanel('organization');
});

it('can render the request password reset page', function () {
    $this->get(Filament::getRequestPasswordResetUrl())
        ->assertSuccessful();
});

it('sends a reset email with an organization panel link', function () {
    Notification::fake();

    $user = User::factory()
        ->withOrganization()
        ->create();

    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => $user->email,
        ])
        ->call('request')
        ->assertNotified();

    Notification::assertSentTo($user, PasswordReset::class, function (PasswordReset $notification) use ($user): bool {
        expect($notification->url)
            ->toContain('/password-reset/reset')
            ->not->toContain('/admin/password-reset/reset');

        $this->get($notification->url)->assertSuccessful();

        return $notification->toMail($user)->subject === __('email.reset_password.subject');
    });
});

it('sends a platform admin an admin panel reset link from the organization panel', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();

    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => $admin->email,
        ])
        ->call('request')
        ->assertNotified();

    Notification::assertSentTo($admin, PasswordReset::class, function (PasswordReset $notification): bool {
        expect($notification->url)->toContain('/admin/password-reset/reset');

        return true;
    });
});
