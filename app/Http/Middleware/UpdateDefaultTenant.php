<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateDefaultTenant
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Filament::auth()->user();
        $tenant = Filament::getTenant();

        if ($user->latest_organization_id !== $tenant->id) {
            $user->update([
                'latest_organization_id' => $tenant->id,
            ]);

            if ($user->userStatus->status === UserStatus::PENDING) {
                $user->userStatus->activate();
            }
        }

        return $next($request);
    }
}
