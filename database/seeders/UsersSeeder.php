<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Mentorship;
use App\Models\MentorshipReq;
use App\Models\MentorshipEntry;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $mentorRole = Role::firstOrCreate(['name' => 'mentor']);
        User::factory()
            ->count(25)
            ->hasDepartment(25)
            ->create();
        $firstUser = User::first();
        $firstUser->password = Hash::make('admin');
        $firstUser->email = 'admin@nafir.net';
        $firstUser->save();

        $commonPassword = Hash::make('password');

        $mentor = User::firstOrCreate(
            ['email' => 'mentor2@nafir.sd'],
            [
                'name'     => 'Mentor 2',
                'password' => $commonPassword,
            ]
        );
        $mentor->assignRole([$userRole, $mentorRole]);

        $mentorships = Mentorship::factory(10)->create([
            'mentor_id' => $mentor->id,
        ]);

        foreach ($mentorships as $index => $mentorship) {
            $menteeNumber = $index + 1;

            $mentee = User::firstOrCreate(
                ['email' => "mentee$menteeNumber@nafir.net"],
                [
                    'name'     => "Mentee $menteeNumber",
                    'password' => $commonPassword,
                ]
            );
            $mentee->assignRole($userRole);

            $request = MentorshipReq::create([
                'mentorship_entry_id' => null,
                'mentee_id'           => $mentee->id,
                'message'             => "I want to join this mentorship.",
                'status'              => 'accepted',
            ]);

            $entry = MentorshipEntry::create([
                'start_date'          => now()->addDays(rand(1, 5)),
                'duration'            => 60,
                'mentorship_id'       => $mentorship->id,
                'accepted_request_id' => $request->id,
                'link'                => 'https://meet.example.com/' . Str::random(10),
                'status'              => 'pending',
            ]);

            $request->update(['mentorship_entry_id' => $entry->id]);
        }

        $user = User::firstOrCreate(
            ['email' => 'user@nafir.net'],
            [
                'name'     => 'Regular User',
                'password' => $commonPassword,
            ]
        );
        $user->assignRole($userRole);

        $admin = User::firstOrCreate(
            ['email' => 'admin@nafir.sd'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('admin'),
            ]
        );
        $admin->assignRole($adminRole);
    }
}
