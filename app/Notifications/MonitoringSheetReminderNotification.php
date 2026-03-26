<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\Beneficiary;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MonitoringSheetReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Beneficiary $beneficiary,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $beneficiary = $this->beneficiary;
        $organization = $beneficiary->organization;

        return [
            'title' => __('monitoring.notifications.sheet_reminder_title'),
            'body' => __('monitoring.notifications.sheet_reminder_body', [
                'name' => $beneficiary->full_name ?? $beneficiary->last_name,
            ]),
            'url' => CaseResource::getUrl('edit_case_monitoring', [
                'tenant' => $organization,
                'record' => $beneficiary,
            ]),
            'beneficiary_id' => $beneficiary->getKey(),
        ];
    }
}
