<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CaseStatus;
use App\Models\Beneficiary;
use App\Models\User;
use App\Notifications\MonitoringSheetReminderNotification;
use Illuminate\Console\Command;

class SendMonitoringSheetRemindersCommand extends Command
{
    protected $signature = 'beneficiaries:send-monitoring-sheet-reminders';

    protected $description = 'Notify case team members to complete monitoring sheets one month after beneficiary registration.';

    public function handle(): int
    {
        $threshold = now()->subMonth();

        Beneficiary::query()
            ->whereNull('monitoring_reminder_sent_at')
            ->where('created_at', '<=', $threshold)
            ->whereIn('status', [CaseStatus::ACTIVE, CaseStatus::MONITORED])
            ->whereDoesntHave('monitoring')
            ->with(['organization', 'specialistsTeam.user'])
            ->chunkById(100, function ($beneficiaries): void {
                foreach ($beneficiaries as $beneficiary) {
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
