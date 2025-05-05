<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Models\MentorshipReq;
use App\Models\Mentorship;
USE App\Models\SuperAdmin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            DepartmentSeeder::class,
            UsersSeeder::class,
            ActivitySeeder::class,
            MentorshipSeeder::class,
            MentorshipreqSeeder::class,
            ActivityReqSeeder::class,
            ActivitesParticpantSeeder::class,
            BlogSeeder::class,
            RoleSeeder::class,
            SuperAdminSeeder::class,
        ]);




    }





}
