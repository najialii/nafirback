<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Clusters\Users;
use Filament\Resources\Pages\ListRecords;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;


class ApproveMentors extends ListRecords
{


    protected static string $resource = UserResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Users::class;

    protected function getTableQuery(): Builder
    {
        return User::query()
            ->whereHas('roles', fn($q) => $q->where('name', 'mentor'))
            ->where('isActive', false);
    }


    public static function getNavigationLabel(): string
    {
        return 'Approve Mentors';
    }


    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return true;
    }




}
