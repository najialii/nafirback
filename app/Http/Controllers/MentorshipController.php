<?php
namespace App\Http\Controllers;

use App\Http\Requests\MentorshipUpdateRequest;
use App\Http\Requests\StoreMentorshipsRequest;
use App\Http\Resources\MentorshipCollection;
use App\Http\Resources\MentorshipResource;
use App\Models\Mentorship;
use App\Models\MentorshipEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                'error'   => 'Something went wrong!',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            return new MentorshipResource(Mentorship::findOrFail($id));

        } catch (\Throwable $th) {
            return response()->json([
                'error'   => 'Something went wrong!',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function search($keyword)
    {
        $mentorships = Mentorship::limit(10)->select(
            [

                "name",
                "id",
            ]
        )->where('name', 'like', '%' . $keyword . '%')->get();

        if (! $mentorships) {
            return response()->json([
                'message' => 'mentorship not found',
            ]);

        }

        return response()->json([
            'mentorship sessions' => $mentorships,

        ], 200);

    }
    public function store(StoreMentorshipsRequest $request)
    {
        $user = Auth()->user();
        try {

            $validatedData = $request->validated();

            if (! $user) {
                return response()->json([
                    'error' => 'Unauthenticated',
                ], 401);
            }

            $validatedData['mentor_id'] = $user->id;

            if ($request->hasFile('img')) {
                $path                 = $request->file('img')->store('mentorships', 'public');
                $validatedData['img'] = '/storage/' . $path;
            }

            $mentorship = Mentorship::create($validatedData);

            $mentorshipEntryData = [
                'mentorship_id' => $mentorship->id,
                'session_date'  => $validatedData['session_date'],
                'duration'      => $validatedData['duration'],
                'link'          => $validatedData['link'] ?? null,
                'status'        => 'pending',
            ];

            $mentorshipEntry = MentorshipEntry::create($mentorshipEntryData);

            return (new MentorshipResource($mentorship))->additional(['mentorship_entry' => $mentorshipEntry]);

        } catch (\Throwable $th) {
            return response()->json([
                'error'   => 'Something went wrong!',
                'message' => $th->getMessage(),
            ], 500);
        }

    }

    public function update(MentorshipUpdateRequest $request, string $id)
    {
        try {
            $mentorship = Mentorship::findOrFail($id);

            $data = $request->validated();

            $mentorship->update($data);

            return response()->json([
                'message' => 'Mentorship updated successfully',
                'data'    => new MentorshipResource($mentorship),
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'error'   => 'Something went wrong',
                'message' => $th->getMessage(),
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
            'data'    => MentorshipCollection::collection($activities),
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
        if (! $mentorships) {
            return response()->json([
                'message' => 'mentorships not found',
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
            $mentorship->delete();

            return response()->json([
                'message' => 'Activity deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error'   => 'Delete failed',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

}
