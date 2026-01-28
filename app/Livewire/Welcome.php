<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class Welcome extends SimplePage
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    /**
     * @var view-string
     */
    protected string $view = 'livewire.welcome';

    public User $user;

    public ?array $data = [];

    public function mount(Request $request): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        if (! $request->hasValidSignature()) {
            abort(Response::HTTP_FORBIDDEN, __('auth.invalid_signature'));
        }

        if (\is_null($this->user)) {
            abort(Response::HTTP_FORBIDDEN, __('auth.invalid_user'));
        }

        if ($this->user->hasSetPassword()) {
            abort(Response::HTTP_FORBIDDEN, __('auth.link_already_used'));
        }

        $this->form->fill([
            'email' => $this->user->email,
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return __('auth.set_password');
    }

    public function handle(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'email' => __('filament::login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }

        $this->user->setPassword(
            data_get($this->form->getState(), 'password')
        );
        $this->user->activate();
        if ($this->user->institution?->isPending()) {
            $this->user->institution->activate();
        }

        Filament::auth()->login($this->user);

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label(__('filament-panels::pages/auth/register.form.email.label'))
                    ->email()
                    ->disabled(),

                TextInput::make('password')
                    ->label(__('filament-panels::pages/auth/register.form.password.label'))
                    ->password()
                    ->rule(
                        [
                            'min:8',
                            'confirmed',
                        ]
                    )
                    ->revealable()
                    ->required()
                    ->confirmed(),

                TextInput::make('password_confirmation')
                    ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
                    ->password()
                    ->revealable()
                    ->required(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('handle')
                ->label(__('auth.set_password'))
                ->submit('handle'),
        ];
    }
}
