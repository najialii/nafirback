<?php

namespace App\Filament\Clusters\MentorshipCluster\Resources\MentorshipReqResource\Pages;

use App\Filament\Clusters\MentorshipCluster\Resources\MentorshipReqResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMentorshipReq extends CreateRecord
{
    protected static string $resource = MentorshipReqResource::class;
}
