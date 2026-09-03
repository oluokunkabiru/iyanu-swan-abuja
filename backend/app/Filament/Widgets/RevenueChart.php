<?php

namespace App\Filament\Widgets;

use App\Models\EventRegistration;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue (last 6 months)';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn ($monthsAgo) => now()->subMonths($monthsAgo)->startOfMonth());

        $revenue = $months->map(function ($month) {
            return (int) EventRegistration::where('payment_status', 'confirmed')
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('amount');
        })->all();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (₦)',
                    'data' => $revenue,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $months->map(fn ($month) => $month->format('M Y'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => ['beginAtZero' => true],
            ],
        ];
    }
}
