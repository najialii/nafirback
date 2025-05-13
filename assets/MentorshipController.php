<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mentorship;
use App\Http\Resources\MentorshipCollection;
use App\Http\Resources\MentorshipResource;
use App\Http\Requests\StoreMentorshipsRequest;
use App\Http\Requests\MentorshipUpdateRequest;
use App\Notifications\MentorshipCreated;
use App\Notifications\MentorshipUpdated;
use App\Notifications\MentorshipDeleted; // Import the new notification
use App\Models\User;

class MentorshipController extends Controller
{
    //

    public function index()
    {
        try {
            $mentorship = Mentorship::paginate(10);
            return new MentorshipCollection($mentorship);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            return new MentorshipResource(Mentorship::findOrFail($id));


        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function search($keyword)
    {
        $mentorships = Mentorship::limit(10)->select(
            [

                "name",
                "id"
            ]
        )->where('name', 'like', '%' . $keyword . '%')->get();

        if (!$mentorships) {
            return response()->json([
                'message' => 'mentorship not found'
            ]);

        }

        return response()->json([
            'mentorship sessions' => $mentorships,

        ], 200);


    }

    public function store(StoreMentorshipsRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['days'] = json_encode($validatedData['days']);
            $validatedData['available_times'] = json_encode($validatedData['available_times']);
            $mentorship = Mentorship::create($validatedData);

            // --- Notification Logic ---
            // This is a simplified example. You will need to fetch actual admins, mentors, etc.
            // For example, to notify admins:
            // $admins = User::where('role', 'admin')->get(); // Adjust based on your role system
            // if ($admins) {
            //     Notification::send($admins, new MentorshipCreated($mentorship));
            // }

            // Notify the user who created the mentorship (assuming they are logged in)
            $creator = $request->user();
            if ($creator) {
                $creator->notify(new MentorshipCreated($mentorship));
            }

            // Example: Notify a specific mentor if mentor_id is available
            // if ($mentorship->mentor_id) {
            //    $mentor = User::find($mentorship->mentor_id);
            //    if ($mentor) {
            //        $mentor->notify(new MentorshipCreated($mentorship));
            //    }
            // }

            return new MentorshipResource($mentorship);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'something went wrong!',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(MentorshipUpdateRequest $request, string $id)
    {
        try {
            $mentorship = Mentorship::findOrFail($id);

            $data = $request->validated();

            $mentorship->update($data);

            // --- Notification Logic for Update ---
            // You would create a new Notification, e.g., MentorshipUpdated
            // And then notify relevant users, potentially highlighting what changed.
            // Example:
            // $creator = $request->user();
            // if ($creator) {
            //    // Assuming you create an App\Notifications\MentorshipUpdated class
            //    // $creator->notify(new MentorshipUpdated($mentorship));
            // }
            // Notify admins, mentor, mentees as needed.
            // --- End Notification Logic for Update ---

            return response()->json([
                'message' => 'Mentorship updated successfully',
                'data' => new MentorshipResource($mentorship)
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function filter(Request $request)
    {
        $query = Mentorship::query();

        if ($request->has('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        // if ($request->has('type')) {
        //     $query->where('type', $request->input('type'));
        // }

        if ($request->has('date')) {
            $query->whereDate('date', $request->input('date'));
        }

        if ($request->has('time')) {
            $query->whereTime('time', $request->input('time'));
        }

        $activities = $query->paginate(10);

        return response()->json([
            'message' => 'Activities retrieved successfully',
            'data' => MentorshipCollection::collection($activities)
        ]);
    }


    public function searchMentorships($keyword)
    {
        $mentorships = Mentorship::limit(10)->select(
            [
                'id',
                'name',
            ]
        );
        $mentorships = $mentorships->where('name', 'like', '%' . $keyword . '%')->get();
        if (!$mentorships) {
            return response()->json([
                'message' => 'mentorships not found'
            ]);
        }
        return response()->json([
            'mentorships' => $mentorships,

        ], 200);
    }

    public function destroy(string $id)
    {
        try {
            $mentorship = Mentorship::findOrFail($id);
            $mentorshipName = $mentorship->name; // Store name before deleting
            $mentorship->delete();

            // --- Notification Logic ---
            // Notify admins or relevant users
            // Example: Notify all users with a specific role (e.g., 'admin')
            // $admins = User::where('role', 'admin')->get();
            // if ($admins->isNotEmpty()) {
            //     Notification::send($admins, new MentorshipDeleted($mentorshipName));
            // }

            // Notify the user who deleted the mentorship (if applicable and logged in)
            // $deleter = auth()->user(); // Or $request->user() if $request is available
            // if ($deleter) {
            //     $deleter->notify(new MentorshipDeleted($mentorshipName));
            // }
            // --- End Notification Logic ---

            return response()->json([
                'message' => 'Activity deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Delete failed',
                'message' => $th->getMessage()
            ], 500);
        }
    }

}
