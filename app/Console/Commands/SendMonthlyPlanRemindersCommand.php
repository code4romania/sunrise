<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CaseStatus;
use App\Models\MonthlyPlan;
use App\Models\User;
use App\Notifications\MonthlyPlanReminderNotification;
use Illuminate\Console\Command;

class SendMonthlyPlanRemindersCommand extends Command
{
    protected $signature = 'monthly-plans:send-reminders';

    protected $description = 'Notify case team members one month after monthly plan creation.';

    public function handle(): int
    {
        $threshold = now()->subMonth();

        MonthlyPlan::query()
            ->whereNull('reminder_sent_at')
            ->where('created_at', '<=', $threshold)
            ->whereHas('beneficiary', fn ($query) => $query->whereIn('status', [CaseStatus::ACTIVE, CaseStatus::MONITORED]))
            ->with(['beneficiary.organization', 'beneficiary.specialistsTeam.user'])
            ->chunkById(100, function ($monthlyPlans): void {
                foreach ($monthlyPlans as $monthlyPlan) {
                    $this->processMonthlyPlan($monthlyPlan);
                }
            });

        return self::SUCCESS;
    }

    protected function processMonthlyPlan(MonthlyPlan $monthlyPlan): void
    {
        $beneficiary = $monthlyPlan->beneficiary;
        if ($beneficiary === null) {
            $monthlyPlan->forceFill(['reminder_sent_at' => now()])->saveQuietly();

            return;
        }

        $userIds = $beneficiary->specialistsTeam
            ->pluck('user_id')
            ->unique()
            ->filter()
            ->values();

        if ($userIds->isEmpty()) {
            $monthlyPlan->forceFill(['reminder_sent_at' => now()])->saveQuietly();

            return;
        }

        $notification = new MonthlyPlanReminderNotification($monthlyPlan);

        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if ($user !== null) {
                $user->notify($notification);
            }
        }

        $monthlyPlan->forceFill(['reminder_sent_at' => now()])->saveQuietly();
    }
}
