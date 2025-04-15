<?php

namespace App\Filament\Resources\MentorshipResource\Pages;

use App\Filament\Resources\MentorshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\MentorshipResource\Widgets\MentorshipsStats;

class ListMentorships extends ListRecords
{
    protected static string $resource = MentorshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MentorshipsStats::class
        ];
    }
}
