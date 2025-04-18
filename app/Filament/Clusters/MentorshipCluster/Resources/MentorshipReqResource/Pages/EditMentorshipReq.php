<?php

namespace App\Filament\Clusters\MentorshipCluster\Resources\MentorshipReqResource\Pages;

use App\Filament\Clusters\MentorshipCluster\Resources\MentorshipReqResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMentorshipReq extends EditRecord
{
    protected static string $resource = MentorshipReqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
