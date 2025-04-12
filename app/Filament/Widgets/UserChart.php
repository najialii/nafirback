<?php

namespace App\Filament\Widgets;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\User;



use Filament\Widgets\ChartWidget;

class UserChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Users';

    protected function getData(): array
    {
$trend = Trend::model(User::class)
->between(
        start: now()->startOfYear(),
        end: now()->endOfYear(),
)->perMonth()->count();

        return [
            //
            'labels' => $trend->map(fn (TrendValue $value) => $value->date),
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $trend->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
