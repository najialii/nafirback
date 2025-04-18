<?php

namespace App\Filament\Resources\ActivitiesResource\Pages;

use App\Filament\Resources\ActivitiesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateActivities extends CreateRecord
{

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // 👈 كدا خلاص
        return $data;
    }

    protected static string $resource = ActivitiesResource::class;
}
