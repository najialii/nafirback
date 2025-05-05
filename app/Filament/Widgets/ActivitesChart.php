<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ActivitiesChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Activities';

    protected function getData(): array
    {

        return [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
