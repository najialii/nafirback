<?php

namespace App\Filament\Clusters\MentorshipCluster\Resources\MentorshipReqResource\Pages;

use App\Filament\Clusters\MentorshipCluster\Resources\MentorshipReqResource;
use App\Models\MentorshipReq;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;



class ListMentorshipReqs extends ListRecords
{
    protected static string $resource = MentorshipReqResource::class;

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
            return MentorshipReq::query();
        }
if($user->hasRole('admin')) {
    return MentorshipReq::query()->whereHas('department', function ($query) use ($user) {
        $query->where('department_id', $user->department_id);
    });
}

    return MentorshipReq::query()->whereHas('mentor', function ($query) use ($user) {
        $query->where('mentor_id', $user->id);
    });
    }
}
