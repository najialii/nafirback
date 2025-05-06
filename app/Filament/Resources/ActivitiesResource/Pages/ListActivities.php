<?php

namespace App\Filament\Resources\ActivitiesResource\Pages;

use App\Filament\Resources\ActivitiesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ActivitiesResource\Widgets\ActivitiesStats;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Auth;
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


    protected function getTableQuery(): Builder
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return Activity::query();
        }

        return Activity::query()->whereHas('department', function ($query) use ($user) {
            $query->where('department_id', $user->department_id);
        });
    }

}
