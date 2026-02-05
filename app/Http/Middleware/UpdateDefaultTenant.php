<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Filament\Organizations\Pages\Dashboard;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateDefaultTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Filament::auth()->user();
        $tenant = Filament::getTenant();
        if (! empty($tenant)) {
            if ($user->latest_organization_id !== $tenant->id) {
                $user->update([
                    'latest_organization_id' => $tenant->id,
                ]);

                if ($tenant->institution->isInactivated()) {
                    return redirect()->to(Dashboard::getUrl());
                }

                if (! $user->userStatus) {
                    $user->initializeStatus();
                    $user->loadMissing('userStatus');
                }

                if ($user->userStatus->status === UserStatus::PENDING) {
                    $user->userStatus->activate();
                }

                if ($tenant->institution->isPending()) {
                    $tenant->institution->activate();
                }
            }
        }

        return $next($request);
    }
}
