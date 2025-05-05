<?php

namespace App\Filament\Resources\ActivitiesResource\Pages;

use App\Filament\Resources\ActivitiesResource;
use Filament\Resources\Pages\ViewRecord;

class ViewActivities extends ViewRecord
{
    protected static string $resource = ActivitiesResource::class;

    public function getView(): string
    {
        return 'filament.resources.activities-resource.pages.view-activities';
    }
}
