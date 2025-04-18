<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Activity;
use App\Models\Mentorship;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Facades\Auth;

class SuperAdminStats extends BaseWidget
{

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();

        if($user->hasRole('super_admin')){
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

        }    else {
            $thirtyDaysUsersCount = 0;
            $lastThirtyDaysActivitiesCount = Activity::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            $lastThirtyDaysMentorshipsCount = Mentorship::where('mentor_id', $user->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
        }


// $successfullActivites = Activity::where('date', '>=', Carbon::now());

        return [


            Stat::make('New Users', $thirtyDaysUsersCount)->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
             ->description('New users in the last 30 days')->color('success')              ->description('7% increase')
             ->chart([7, 2, 10, 3, 15, 4, 17])
             ,

            //  ->chart($newUsersChartData),

             Stat::make('New Activities', $lastThirtyDaysActivitiesCount)->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
             ->description('17% decress')
            //  ->chart($newActivitiesChartData)
             ->color('success')            ->chart([100, 80, 10, 3, 0, 0, 0])
             ,

             Stat::make('New Mentorships', $lastThirtyDaysMentorshipsCount)->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
             ->description('New mentorships in the last 30 days')
             ->color('success')              ->description('7% increase')
             ->chart([7, 2, 10, 3, 15, 4, 17])



        ];
    }
}
