<?php

namespace App\Filament\Widgets;

use App\Models\MemberProfile;
use Filament\Widgets\ChartWidget;

class MembershipStatusChart extends ChartWidget
{
    protected ?string $heading = 'Membership breakdown';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $counts = MemberProfile::query()
            ->selectRaw('membership_status, count(*) as total')
            ->groupBy('membership_status')
            ->pluck('total', 'membership_status');

        return [
            'datasets' => [
                [
                    'label' => 'Members',
                    'data' => [
                        $counts->get('active', 0),
                        $counts->get('pending', 0),
                        $counts->get('expired', 0),
                    ],
                    'backgroundColor' => ['#22c55e', '#f59e0b', '#ef4444'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Active', 'Pending', 'Expired'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
