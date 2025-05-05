<?php

namespace App\Filament\Resources\DepartmentResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Activity;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;

class DepartmentsStats extends BaseWidget
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

        $depAdminsCount = User::where('role', 'admin')->count();
        $mentorCount = User::where('role', 'mentor')->count();
        $menteeCount = User::where('role', 'mentee')->count();

        $total = $depAdminsCount + $mentorCount + $menteeCount;


        $UserPartActivites = Activity::where('user_id')->get()
            ->sum(function ($activity) {
                return is_array($activity->participants);
            });



        //departments with most activites

        return [
            Stat::make('New Users', $thirtyDaysUsersCount)->icon('heroicon-m-user-group', IconPosition::Before)
                ->description('New users in the last 30 days')->color('primary')->chart($newUsersChartData),

            Stat::make('Users by Role', "{$total}  Users/Role")
                ->description("Admins: {$depAdminsCount} | Mentors: {$mentorCount} | Mentees: {$menteeCount}")
                ->icon('heroicon-o-users')
                ->color('primary'),


            Stat::make('Total Activity Participants', $UserPartActivites)->description('Total participants in your activities')
                ->icon('heroicon-o-user-group')->color('warning'),
        ];
    }
}
