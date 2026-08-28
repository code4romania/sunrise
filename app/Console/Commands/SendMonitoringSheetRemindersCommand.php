<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CaseStatus;
use App\Models\Beneficiary;
use App\Models\User;
use App\Notifications\MonitoringSheetReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SendMonitoringSheetRemindersCommand extends Command
{
    protected $signature = 'beneficiaries:send-monitoring-sheet-reminders';

    protected $description = 'Notify case team members one month after the last monitoring sheet was created.';

    public function handle(): int
    {
        $threshold = now()->subMonth();

        Beneficiary::query()
            ->whereIn('status', [CaseStatus::ACTIVE, CaseStatus::MONITORED])
            ->whereHas('monitoring', fn (Builder $query) => $query->where('created_at', '<=', $threshold))
            ->with(['organization', 'specialistsTeam.user', 'lastMonitoring'])
            ->chunkById(100, function ($beneficiaries): void {
                foreach ($beneficiaries as $beneficiary) {
                    $lastMonitoring = $beneficiary->lastMonitoring;
                    if ($lastMonitoring === null) {
                        continue;
                    }

                    if (
                        $beneficiary->monitoring_reminder_sent_at !== null
                        && $beneficiary->monitoring_reminder_sent_at->greaterThan($lastMonitoring->created_at)
                    ) {
                        continue;
                    }

                    $this->processBeneficiary($beneficiary);
                }
            });

        return self::SUCCESS;
    }

    protected function processBeneficiary(Beneficiary $beneficiary): void
    {
        $userIds = $beneficiary->specialistsTeam
            ->pluck('user_id')
            ->unique()
            ->filter()
            ->values();

        if ($userIds->isEmpty()) {
            $beneficiary->forceFill(['monitoring_reminder_sent_at' => now()])->saveQuietly();

            return;
        }

        $notification = new MonitoringSheetReminderNotification($beneficiary);

        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if ($user !== null) {
                $user->notify($notification);
            }
        }

        $beneficiary->forceFill(['monitoring_reminder_sent_at' => now()])->saveQuietly();
    }
}
