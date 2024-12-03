<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserStatus as UserStatusEnum;
use App\Models\Scopes\BelongsToCurrentTenant;
use App\Models\User;
use App\Models\UserStatus;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->userAndInstitutionIsActive()) {
            return $next($request);
        }

        Filament::auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(Filament::getCurrentPanel()->getLoginUrl())
            ->with('error', __('user.inactive_error'));
    }

    public function userAndInstitutionIsActive(): bool
    {
        $userActiveOrganizations = UserStatus::query()
            ->where('user_id', auth()->id())
            ->withoutGlobalScopes([BelongsToCurrentTenant::class])
            ->with('organization.institution')
            ->whereIn('status', [UserStatusEnum::PENDING->value, UserStatusEnum::ACTIVE->value])
            ->get()
            ->filter(fn (UserStatus $userStatus) => ! $userStatus->organization?->institution->isInactivated());

        if ($userActiveOrganizations->isEmpty()) {
            return false;
        }

        /** @var User $user */
        $user = auth()->user();

        if ($user->isAdmin() && Filament::getCurrentPanel()->getId() === 'admin')
        {
            return UserStatus::query()
                ->where('user_id', auth()->id())
                ->whereNull('organization_id')
                ->withoutGlobalScopes([BelongsToCurrentTenant::class])
                ->whereIn('status', [UserStatusEnum::PENDING->value, UserStatusEnum::ACTIVE->value])
                ->exists();
        }

        if ($userActiveOrganizations->pluck('organization_id')->contains($user->latest_organization_id)) {
            return true;
        }

        $user->latest_organization_id = $userActiveOrganizations
            ->first()
            ?->organization_id;

        $user->save();

        if ($user->latest_organization_id) {
            return $this->userAndInstitutionIsActive();
        }

        return false;
    }
}
