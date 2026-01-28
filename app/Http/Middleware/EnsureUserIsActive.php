<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\InstitutionStatus;
use App\Enums\UserStatus as UserStatusEnum;
use App\Filament\Organizations\Pages\Dashboard;
use App\Models\Scopes\BelongsToCurrentTenant;
use App\Models\User;
use App\Models\UserStatus;
use Closure;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Filament::getCurrentOrDefaultPanel()->getId() === 'admin' && auth()->user()->isAdmin()) {
            return $next($request);
        }
        if ($this->userAndInstitutionIsActive()) {
            return $next($request);
        }
        $user = auth()->user();

        if (Filament::getCurrentOrDefaultPanel()->getId() === 'organization') {
            $activeOrganization = UserStatus::query()
                ->withoutGlobalScopes([BelongsToCurrentTenant::class])
                ->where('user_id', auth()->id())
                ->whereIn('status', [UserStatusEnum::PENDING->value, UserStatusEnum::ACTIVE->value])
                ->whereHas(
                    'institution',
                    fn (Builder $query) => $query->whereIn('status', [
                        InstitutionStatus::ACTIVE->value,
                        InstitutionStatus::PENDING->value,
                    ])
                )
                ->first();

            if ($activeOrganization) {
                $user->update(['latest_organization_id' => $activeOrganization->organization_id]);

                return redirect()->to(Dashboard::getUrl(['tenant' => $activeOrganization->organization]));
            }
        }

        Filament::auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Notification::make()
            ->title(__('user.inactive_error.title', [
                'seconds' => 10,
            ]))
            ->body(\array_key_exists('body', __('user.inactive_error') ?: []) ? __('user.inactive_error.body', [
                'seconds' => 10,
            ]) : null)
            ->danger()->send();

        return redirect()->to(Filament::getCurrentOrDefaultPanel()->getLoginUrl());
    }

    public function userAndInstitutionIsActive(): bool|RedirectResponse
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        $query = UserStatus::query()
            ->where('user_id', $user->id)
            ->withoutGlobalScopes([BelongsToCurrentTenant::class])
            ->whereIn('status', [UserStatusEnum::PENDING->value, UserStatusEnum::ACTIVE->value]);

        if ($user->isAdmin() && Filament::getCurrentOrDefaultPanel()->getId() === 'admin') {
            $query->whereNull('organization_id');
        } else {
            $query->where('organization_id', $user->latest_organization_id)
                ->whereHas(
                    'institution',
                    fn (Builder $query) => $query->whereIn('status', [
                        InstitutionStatus::ACTIVE->value,
                        InstitutionStatus::PENDING->value,
                    ])
                );
        }

        return $query->exists();
    }
}
