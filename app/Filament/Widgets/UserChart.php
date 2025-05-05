<?php

namespace App\Filament\Widgets;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\User;



use Filament\Widgets\ChartWidget;

class UserChart extends ChartWidget
{
    protected static ?int $sort = 2;
    public ?string $filter = 'today';
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
            'labels' => $trend->map(fn(TrendValue $value) => $value->date),
            'datasets' => [
                [
                    'label' => 'User per month',
                    'backgroundColor' => 'rgb(34, 31, 66)',
                    'borderColor' => 'primary',
                    'color' => 'primary',
                    'data' => $trend->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
        ];
    }


    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
