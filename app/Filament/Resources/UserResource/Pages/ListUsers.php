<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\UserResource\Widgets\UserStats;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{


    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return User::query();
        }

        return User::query()->whereHas('department', function ($query) use ($user) {
            $query->where('department_id', $user->department_id);
        });
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStats::class
        ];
    }
}
