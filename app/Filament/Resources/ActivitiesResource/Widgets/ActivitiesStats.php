<?php

namespace App\Filament\Resources\ActivitiesResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Activity;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;

class ActivitiesStats extends BaseWidget
{
    protected function getStats(): array
    {
        $thirtyDaysUsersCount = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $newUsersData = User::where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('COUNT(*) as count, DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
        $newUsersChartData = array_values($newUsersData);



        $UserPartActivites = Activity::where('user_id')->get()
            ->sum(function ($activity) {
                return is_array($activity->participants);
            });

        $newActivitiesCount = Activity::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        // $upcomingActivitiesCount = Activity::where('created_at', '>=', Carbon::now())->count();
        // $averageParticipants = Activity::withCount('crea')->get()->avg('participants_count');


        return [
            Stat::make('New Activites', $newActivitiesCount)->icon('heroicon-m-user-group', IconPosition::Before)
                ->description('New users in the last 30 days')->color('primary')->chart($newUsersChartData),

            // Stat::make('upcoming Activities', $upcomingActivitiesCount)->icon('heroicon-m-user-group', IconPosition::Before)
            // ->description('New users in the last 30 days')->color('primary')->chart($newUsersChartData),
            // Stat::make('upcoming Activities', $averageParticipants)->icon('heroicon-m-user-group', IconPosition::Before)
            // ->description('New users in the last 30 days')->color('primary')->chart($newUsersChartData),


            Stat::make('Total Activity Participants', $UserPartActivites)->description('Total participants in your activities')
                ->icon('heroicon-o-user-group')->color('warning'),

        ];
    }
}
