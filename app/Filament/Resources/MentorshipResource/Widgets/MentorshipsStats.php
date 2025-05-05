<?php

namespace App\Filament\Resources\MentorshipResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Mentorship;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;

class MentorshipsStats extends BaseWidget
{
    protected function getStats(): array
    {



        $ThirtyDaysMentorshipsCount = Mentorship::where('created_at', '>=', Carbon::now()->subDays(30))->count();





        return [
            Stat::make('New Users', $ThirtyDaysMentorshipsCount)->icon('heroicon-m-user-group', IconPosition::Before)
                ->description('New users in the last 30 days')->color('primary'),
            // Stat::make('New Users', $moReqPerDepar)->icon('heroicon-m-user-group', IconPosition::Before)
            // ->description('most requsted ')->color('primary'),


        ];
    }
}
