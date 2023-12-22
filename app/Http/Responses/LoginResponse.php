<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = Filament::auth()->user();

        if ($user->is_admin) {
            $panel = Filament::getPanel('admin');
            $parameters = [];
        } else {
            $panel = Filament::getPanel('organization');
            $parameters = $user->getDefaultTenant($panel);
        }

        return redirect()->intended($panel->route('pages.dashboard', $parameters));
    }
}
