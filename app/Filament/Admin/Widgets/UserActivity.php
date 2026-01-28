<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Activity;
use DB;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class UserActivity extends ChartWidget
{
    protected ?string $heading = 'Chart';

    protected int | string | array $columnSpan = 2;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $userActivity = Activity::withoutGlobalScope('latest')
            ->toBase()
            ->where('subject_type', 'user')
            ->where('event', 'logged_in')
            ->where('created_at', '>=', now()->subYear())
            ->select([
                DB::raw('count(distinct(subject_id)) as total'),
                DB::raw("DATE_FORMAT(created_at,'%Y %m') as year_and_month"),
            ])
            ->groupBy('year_and_month')
            ->orderBy('year_and_month')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('user.label.plural'),
                    'data' => $userActivity->map(fn ($value) => $value->total),
                ],
            ],
            'labels' => $userActivity->map(function ($value) {
                $month = explode(' ', $value->year_and_month)[1];

                return __('enum.short_months.' . (int) $month);
            }),
        ];
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('user.heading.active_users');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('user.placeholders.dashboard_cart');
    }

    protected function getType(): string
    {
        return 'line';
    }
}
