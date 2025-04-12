<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Activity;
use App\Models\Mentorship;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;

class SuperAdminStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $thirtyDaysUsersCount = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $lastThirtyDaysActivitiesCount = Activity::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $lastThirtyDaysMentorshipsCount = Mentorship::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        $newUsersData = User::where('created_at', '>=', Carbon::now()->subDays(30))
                            ->selectRaw('COUNT(*) as count, DATE(created_at) as date')
                            ->groupBy('date')
                            ->orderBy('date')
                            ->get()
                            ->pluck('count', 'date')
                            ->toArray();

    

        $newUsersChartData = array_values($newUsersData);  

        return [
            
            
            Stat::make('New Users', $thirtyDaysUsersCount)->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
             ->description('New users in the last 30 days')->color('success')->chart($newUsersChartData),

             Stat::make('New Activities', $lastThirtyDaysActivitiesCount)->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
             ->description('New activities in the last 30 days')
            //  ->chart()
             ->color('success'),

             Stat::make('New Mentorships', $lastThirtyDaysMentorshipsCount)->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
             ->description('New mentorships in the last 30 days')
             ->color('success')


        ];
    }
}
