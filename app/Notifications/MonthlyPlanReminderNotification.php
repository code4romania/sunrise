<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\MonthlyPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MonthlyPlanReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public MonthlyPlan $monthlyPlan,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $monthlyPlan = $this->monthlyPlan;
        $beneficiary = $monthlyPlan->beneficiary;
        $organization = $beneficiary?->organization;

        return [
            'title' => __('intervention_plan.notifications.monthly_plan_reminder_title'),
            'body' => __('intervention_plan.notifications.monthly_plan_reminder_body', [
                'name' => $beneficiary?->full_name ?? '—',
            ]),
            'url' => CaseResource::getUrl('view_monthly_plan', [
                'tenant' => $organization,
                'record' => $beneficiary,
                'monthlyPlan' => $monthlyPlan,
            ]),
            'beneficiary_id' => $beneficiary?->getKey(),
            'monthly_plan_id' => $monthlyPlan->getKey(),
        ];
    }
}
