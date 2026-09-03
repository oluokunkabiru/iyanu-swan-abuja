<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\ChartWidget;

class EventRegistrationsChart extends ChartWidget
{
    protected ?string $heading = 'Registrations by event';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $events = Event::query()
            ->withCount([
                'registrations as confirmed_count' => fn ($query) => $query->where('payment_status', 'confirmed'),
                'registrations as pending_count' => fn ($query) => $query->where('payment_status', 'pending'),
            ])
            ->having('confirmed_count', '>', 0)
            ->orHaving('pending_count', '>', 0)
            ->orderByDesc('starts_at')
            ->limit(6)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Confirmed',
                    'data' => $events->pluck('confirmed_count')->all(),
                    'backgroundColor' => '#6366f1',
                ],
                [
                    'label' => 'Pending',
                    'data' => $events->pluck('pending_count')->all(),
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $events->pluck('title')->map(fn ($title) => (string) str($title)->limit(20))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]],
            ],
        ];
    }
}
