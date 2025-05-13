<?php
namespace App\Http\Controllers;

use App\Http\Requests\MentorshipUpdateRequest;
use App\Http\Requests\StoreMentorshipsRequest;
use App\Http\Resources\MenteeMenttorshipResource;
use App\Http\Resources\MentorshipCollection;
use App\Http\Resources\MentorshipResource;
use App\Models\Mentorship;
use App\Models\MentorshipEntry;
use App\Models\MentorshipReq;
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
    public function paginated_index(Request $request)
    {
        // TODO: Decide on which to add
        try {
            $query = Mentorship::query();
            \App\Filters\BasicSearchFilter::apply($query, $request->all(), ['name']);
            $mentorships = $query->paginate($request->input('per_page', 10));
            return (new MentorshipCollection($mentorships))
                ->additional(['message' => 'Mentorships retrieved successfully']);
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

 public function getMentorshipsByMentee()
 {
  try {

   $entries = Mentorship::with(['entries', 'mentor'])->paginate(10);

   return response()->json([
    'data' => MenteeMenttorshipResource::collection($entries),
   ]);

   // return response()->json([
   //     'message' => 'Mentee mentorship sessions retrieved successfully',
   //     $entries
   // ]);
  } catch (\Throwable $th) {
   return response()->json([
    'error'   => 'Failed to retrieve mentorship sessions',
    'message' => $th->getMessage(),
   ], 500);
  }
 }

 public function getOneMentorshipForMentee($id)
 {
     try {
         $user = Auth::user();
 
         $mentorship = Mentorship::with(['entries', 'mentor'])->findOrFail($id);
 
         // Get all entry IDs for this mentorship
         $entryIds = $mentorship->entries->pluck('id');
 
         // Find the user's request to any of the entries
         $userRequest = MentorshipReq::whereIn('mentorship_entry_id', $entryIds)
             ->where('mentee_id', $user->id)
             ->orderByRaw("FIELD(status, 'accepted', 'pending', 'rejected')") // optional: prioritize accepted
             ->first();
 
         // Set the user status based on request
         $userStatus = $userRequest ? $userRequest->status : 'not_booked';
 
         return response()->json([
             'data' => (new MenteeMenttorshipResource($mentorship))->additional([
                 'user_status' => $userStatus
             ]),
         ]);
     } catch (\Throwable $th) {
         return response()->json([
             'error'   => 'Failed to retrieve mentorship session',
             'message' => $th->getMessage(),
         ], 500);
     }
 }
 

 public function getMentorshipsByMentor()
 {
  try {
   $user = Auth::user();

   $mentorships = Mentorship::where('mentor_id', $user->id)
    ->with('entries')
    ->paginate(10);

   return response()->json([
    'message' => 'Mentor mentorship sessions retrieved successfully',
    'data'    => MentorshipCollection::make($mentorships),
   ]);
  } catch (\Throwable $th) {
   return response()->json([
    'error'   => 'Failed to retrieve mentorship sessions',
    'message' => $th->getMessage(),
   ], 500);
  }
 }

 public function getOneMentorshipForMentor($id)
 {
  try {
   $user = Auth::user();

   $mentorship = Mentorship::with(['entries.request']) // include accepted request
    ->where('id', $id)
    ->where('mentor_id', $user->id)
    ->firstOrFail();

   return response()->json([
    'message' => 'Mentor mentorship session retrieved successfully',
    'data'    => new MentorshipResource($mentorship),
   ]);
  } catch (\Throwable $th) {
   return response()->json([
    'error'   => 'Failed to retrieve session for mentor',
    'message' => $th->getMessage(),
   ], 500);
  }
 }

}
