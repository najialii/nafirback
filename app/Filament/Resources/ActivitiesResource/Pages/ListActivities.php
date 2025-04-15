<?php

namespace App\Filament\Resources\ActivitiesResource\Pages;

use App\Filament\Resources\ActivitiesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\use;
use App\Filament\Resources\ActivitiesResource\Widgets\ActivitiesStats;
class ListActivities extends ListRecords
{
    protected static string $resource = ActivitiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ActivitiesStats::class
        ];
    }
}
