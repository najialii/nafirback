<?php
namespace App\Http\Controllers;

use App\Models\Mentorship;
use App\Models\MentorshipEntry;
use App\Models\MentorshipReq;
use function Pest\Laravel\json;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorshipReqController extends Controller
{
 //

 public function index()
 {
  try {
   $mentorshipRequests = MentorshipReq::with(['mentee', 'mentor', 'mentorship'])
    ->select('id', 'mentorship_id', 'mentor_id', 'mentee_id', 'sele_date', 'sele_time', 'message', 'status', 'created_at', 'updated_at')
    ->paginate(10);

   return response()->json($mentorshipRequests, 200);
  } catch (\Throwable $th) {
   return response()->json([
    'error'   => 'Something went wrong',
    'message' => $th->getMessage(),
   ], 500);
  }
 }

 public function getAllMentorReq()
 {
  // mjdmd
  $user = auth()->user();
  try {

   $mentorshipReq = MentorshipReq::where('mentor_id', $user->id)
    ->with(['mentee', 'mentorship'])
    ->select('id', 'mentorship_id', 'mentee_id', 'sele_date', 'sele_time', 'message', 'status', 'created_at', 'updated_at')
    ->get();

   return response()->json([
    'status' => $mentorshipReq->status,
    'body'   => [
     'id'            => $mentorshipReq->id,
     'mentorship_id' => $mentorshipReq->mentorship_id,
     'mentee'        => [
      'id'          => $mentorshipReq->mentee->id,
      'name'        => $mentorshipReq->mentee->name,
      'profile_pic' => $mentorshipReq->mentee->profile_pic,
     ],
     'sele_date'     => $mentorshipReq->sele_date,
     'sele_time'     => $mentorshipReq->sele_time,
     'message'       => $mentorshipReq->message,
     'created_at'    => $mentorshipReq->created_at,
     'updated_at'    => $mentorshipReq->updated_at,
    ],

   ], 200);

  } catch (\Throwable $th) {
   return response()->json([
    'error'   => 'Something went wrong',
    'message' => $th->getMessage(),
   ], 500);
  }
 }



 public function getMentorRequests()
 {
     $mentorId = auth()->id();
 
     try {
         $mentorshipEntries = MentorshipEntry::whereHas('mentorship', function ($query) use ($mentorId) {
             $query->where('mentor_id', $mentorId);
         })->with('mentorship.mentor')->get(); 
 
         $requests = MentorshipReq::whereIn('mentorship_entry_id', $mentorshipEntries->pluck('id'))
             ->with(['mentee', 'mentorship_entry'])  
             ->get();
 
         $filteredRequests = $requests->filter(function ($request) use ($mentorId) {
             return $request->mentorship_entry->mentorship->mentor->id === $mentorId;
         });
 
         return response()->json($filteredRequests->values(), 200); 
     } catch (\Throwable $th) {
         return response()->json([
             'error'   => 'Something went wrong',
             'message' => $th->getMessage(),
         ], 500);
     }
 }
 
 public function getMenteeRequests()
 {
     $mentorId = auth()->id();
 
     try {
         $mentorshipEntries = MentorshipEntry::whereHas('mentorship', function ($query) use ($mentorId) {
             $query->where('mentor_id', $mentorId);
         })->with('mentorship.mentor')->get(); 
 
         $requests = MentorshipReq::whereIn('mentorship_entry_id', $mentorshipEntries->pluck('id'))
             ->with(['mentee', 'mentorship_entry'])  
             ->get();
 
         $filteredRequests = $requests->filter(function ($request) use ($mentorId) {
             return $request->mentorship_entry->mentorship->mentor->id === $mentorId;
         });
 
         return response()->json($filteredRequests->values(), 200); 
     } catch (\Throwable $th) {
         return response()->json([
             'error'   => 'Something went wrong',
             'message' => $th->getMessage(),
         ], 500);
     }
 }



 public function reqSession(Request $request, $mentorshipEntryId)
 {
     $user = Auth::user();

     try {
         $validatedData = $request->validate([
             'message'   => 'nullable|string',
             'session_date' => 'required|date',
        
         ]);

         $mentorshipEntry = DB::transaction(function () use ($mentorshipEntryId, $user, $validatedData) {
             $mentorshipEntry = MentorshipEntry::find($mentorshipEntryId);

             if (!$mentorshipEntry) {
                 throw new \Exception('The mentorship entry does not exist');
             }

             $mentorship = $mentorshipEntry->mentorship;
              if (!$mentorship) {
                 throw new \Exception('The associated mentorship does not exist');
             }

             $isBooked = MentorshipReq::where('mentorship_entry_id', $mentorshipEntryId)
                 ->where('mentee_id', $user->id)
                 ->exists();

             if ($isBooked) {
                 throw new \Exception('You have already booked this mentorship session');
             }

             if ($mentorship->mentor_id === $user->id) {
                  throw new \Exception('Mentors cannot book their own sessions.');
             }

             $mentorshipReq = MentorshipReq::create([
                 'mentorship_entry_id' => $mentorshipEntryId,
                 'mentor_id'           => $mentorship->mentor_id, 
                 'mentee_id'           => $user->id,
                 'message'             => $validatedData['message'] ?? null,
                 'status'              => 'pending', 
                 'session_date'           => $validatedData['session_date'],
             ]);

             return $mentorshipEntry; 
         });

         $mentorshipReq = MentorshipReq::where('mentorship_entry_id', $mentorshipEntryId)->where('mentee_id', $user->id)->first();
         $mentorshipReq->load(['mentee', 'mentorship_entry.mentorship.mentor']); 

         return response()->json([
             'message' => 'Mentorship request created successfully.',
             'data'    => [
                 'id'                  => $mentorshipReq->id,
                 'mentorship_entry_id' => $mentorshipReq->mentorship_entry_id,
                 'mentee'              => [
                     'id'   => $mentorshipReq->mentee->id,
                     'name' => $mentorshipReq->mentee->name,
                 ],
                 'mentor' => [
                     'id' => $mentorshipReq->mentorship_entry->mentorship->mentor->id,
                     'name' => $mentorshipReq->mentorship_entry->mentorship->mentor->name,
                     'session_date'           => $mentorshipReq->session_date,
                 ],
                 'message'             => $mentorshipReq->message,
                 'status'              => $mentorshipReq->status,
             ],
         ], 201);
     } catch (\Exception $e) {
         return response()->json([
             'error'   => 'Failed to create mentorship request.',
             'message' => $e->getMessage(),
         ], 400); 
     }
 }

    public function processMentorshipReq(Request $request, $id)
    {
        $user = Auth::user();

        try {
            $validatedData = $request->validate([
                'status' => ['required', 'string', 'in:pending,accepted,rejected,completed'],
            ]);

            $mentorshipRequest = DB::transaction(function () use ($id, $user, $validatedData) {
                $mentorshipRequest = MentorshipReq::find($id);

                if (!$mentorshipRequest) {
                    throw new \Exception('Mentorship request not found.');
                }

                $mentorshipEntry = $mentorshipRequest->mentorship_entry;
                if (!$mentorshipEntry)
                {
                     throw new \Exception('Mentorship Entry not found.');
                }
                if ($mentorshipEntry->mentorship->mentor_id !== $user->id) {
                    throw new \Exception('You are not authorized to process this request.');
                }

                $mentorshipRequest->status = $validatedData['status'];
                $mentorshipRequest->save();

                if ($validatedData['status'] === 'accepted' || $validatedData['status'] === 'completed') {
                    $mentorshipEntry->status = $validatedData['status'];
                    $mentorshipEntry->save();
                }
                return $mentorshipRequest;
            });
             $mentorshipRequest->load(['mentee', 'mentorship_entry.mentorship.mentor']);

            return response()->json([
                'message'            => 'Mentorship request status updated successfully.',
                'data' => [
                    'id'                  => $mentorshipRequest->id,
                    'mentorship_entry_id' => $mentorshipRequest->mentorship_entry_id,
                    'mentee'              => [
                        'id'   => $mentorshipRequest->mentee->id,
                        'name' => $mentorshipRequest->mentee->name,
                    ],
                    'mentor' => [
                        'id' => $mentorshipRequest->mentorship_entry->mentorship->mentor->id,
                        'name' => $mentorshipRequest->mentorship_entry->mentorship->mentor->name,
                    ],
                    'status'              => $mentorshipRequest->status,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to process mentorship request.',
                'message' => $e->getMessage(),
            ], 400); 
        }
    }





























 
//  public function getoneMentorReq($mentorshipId)
//  {
//   try {
//    $user = auth()->user();

//    $mentorshipReq = MentorshipReq::where('mentorship_id', $mentorshipId)
//     ->where('mentor_id', $user->id)
//     ->with(['mentee', 'mentorship'])
//     ->select('id', 'mentorship_id', 'mentee_id', 'sele_date', 'sele_time', 'message', 'status', 'created_at', 'updated_at')
//     ->first();

//    if (! $mentorshipReq) {
//     return response()->json([
//      'error'   => 'Mentorship request not found',
//      'message' => 'No mentorship request found for the given mentorship ID',
//     ], 404);
//    }

//    return response()->json([
//     'status' => $mentorshipReq->status,
//     'body'   => [
//      'id'            => $mentorshipReq->id,
//      'mentorship_id' => $mentorshipReq->mentorship_id,
//      'mentee'        => [
//       'id'          => $mentorshipReq->mentee->id,
//       'name'        => $mentorshipReq->mentee->name,
//       'profile_pic' => $mentorshipReq->mentee->profile_pic,
//      ],
//      'sele_date'     => $mentorshipReq->sele_date,
//      'sele_time'     => $mentorshipReq->sele_time,
//      'message'       => $mentorshipReq->message,
//      'created_at'    => $mentorshipReq->created_at,
//      'updated_at'    => $mentorshipReq->updated_at,
//     ],

//    ], 200);
//   } catch (\Throwable $th) {
//    return response()->json([
//     'error'   => 'Something went wrong',
//     'message' => $th->getMessage(),
//    ], 500);
//   }
//  }
 public function destroy($id)
 {
  try {
   $mentorshipReq = MentorshipReq::find($id);

   if (! $mentorshipReq) {
    return response()->json([
     'error'   => 'Mentorship request not found',
     'message' => 'The specified mentorship request does not exist',
    ], 404);
   }

   if (auth()->id() !== $mentorshipReq->mentee_id && auth()->id() !== $mentorshipReq->mentor_id) {
    return response()->json([
     'error'   => 'Unauthorized',
     'message' => 'You are not authorized to delete this mentorship request',
    ], 403);
   }

   $mentorshipReq->delete();

   return response()->json([
    'message' => 'Mentorship request deleted successfully',
   ], 200);
  } catch (\Throwable $th) {
   return response()->json([
    'error'   => 'Something went wrong',
    'message' => $th->getMessage(),
   ], 500);
  }
 }

 public function storesss(Request $request, $id)
 {
  $user = auth()->user();

  try {
   $validatedData = $request->validate([
    'message'   => 'nullable|string',
    'sele_date' => 'required|date',
    'sele_time' => 'required|date_format:H:i',
   ]);

   $mentorship = Mentorship::find($id);
   if (! $mentorship) {
    return response()->json([
     'error'   => 'Invalid mentorship session',
     'message' => 'The mentorship session does not exist',
    ], 404);
   }

   $isBooked = MentorshipReq::where('mentorship_id', $id)
    ->where('mentee_id', $user->id)
    ->exists();

   if ($isBooked) {
    return response()->json([
     'error'   => 'Mentorship session already booked',
     'message' => 'You have already booked this mentorship session',
    ], 400);
   }

   $mentorshipReq = MentorshipReq::create([
    'mentorship_id' => $id,
    'mentor_id'     => $mentorship->mentor_id,
    'mentee_id'     => $user->id,
    'message'       => $validatedData['message'] ?? null,
    'sele_date'     => $validatedData['sele_date'],
    'sele_time'     => $validatedData['sele_time'],
   ]);

   $mentorshipReq->load(['mentee', 'mentor', 'mentorship']);

   return response()->json([
    'id'            => $mentorshipReq->id,
    'mentorship_id' => $mentorshipReq->mentorship_id,
    'sele_date'     => $mentorshipReq->sele_date,
    'sele_time'     => $mentorshipReq->sele_time,
    'message'       => $mentorshipReq->message,
    'status'        => $mentorshipReq->status,
    'created_at'    => $mentorshipReq->created_at,
    'updated_at'    => $mentorshipReq->updated_at,
    'mentee'        => [
     'id'          => $mentorshipReq->mentee->id,
     'name'        => $mentorshipReq->mentee->name,
     'profile_pic' => $mentorshipReq->mentee->profile_pic,
    ],
    'mentor'        => [
     'id'          => $mentorshipReq->mentor->id,
     'name'        => $mentorshipReq->mentor->name,
     'profile_pic' => $mentorshipReq->mentor->profile_pic,
    ],
   ], 201);
  } catch (\Throwable $th) {
   return response()->json([
    'error'   => 'Something went wrong',
    'message' => $th->getMessage(),
   ], 500);
  }
 }

//  public function getMentorRequests($userId)
//  {

//   try {
//    $requests = MentorshipReq::where('mentor_id', $userId)->with(['user', 'mentor', 'mentorship'])->get();

//    return response()->json($requests);
//   } catch (\Throwable $th) {
//    //throw $th;
//    return response()->json([
//     'error'   => 'something went wrong',
//     'message' => $th->getMessage(),
//    ], 500);
//   }

//  }

 
 public function getMentorMentorsRequests($userId)
 {
  try {

   // $requests = MentorshipReq::where('mentor_Id' , $mentorId)->with(['mentee_id', 'mentorship_id'])->get();
   $requests = MentorshipReq::where('mentor_id', $userId)->with(['user', 'mentor', 'mentorship'])->get();

   // return dd($mentorId);
   return response()->json($requests);

   //code...
  } catch (\Throwable $th) {

   return response()->json([
    'error' => $th->getMessage(),
   ], 500);
  }
 }

 

 public function session_reschedule(Request $request, $id)
 {
  $request->validate([
   'selectedDay'  => 'required|date',
   'selectedtime' => 'required|date_format:H:i',
  ]);

  $mentorshipReq = MentorshipReq::find($id);

  if (! $mentorshipReq) {
   return response()->json([
    'error' => 'Mentorship request not found',
   ], 404);
  }

  if (auth()->id() !== $mentorshipReq->mentor_id) {
   return response()->json([
    'error' => 'Unauthorized',
   ], 403);
  }

  $mentorshipReq->reschedule($request->selecteday, $request->selectedtime);

  return response()->json([
   'error' => 'Mentorship request not found',
  ], 404);
 }

 public function show($id)
 {
  try {
   $mentorshipReq = MentorshipReq::with(['mentee', 'mentor', 'mentorship'])
    ->select('id', 'mentorship_id', 'mentor_id', 'mentee_id', 'sele_date', 'sele_time', 'message', 'status', 'created_at', 'updated_at')
    ->find($id);

   if (! $mentorshipReq) {
    return response()->json(['status' => 'unbooked'], 404);
   }

   return response()->json([
    'id'            => $mentorshipReq->id,
    'mentorship_id' => $mentorshipReq->mentorship_id,
    'sele_date'     => $mentorshipReq->sele_date,
    'sele_time'     => $mentorshipReq->sele_time,
    'message'       => $mentorshipReq->message,
    'status'        => $mentorshipReq->status,
    'created_at'    => $mentorshipReq->created_at,
    'updated_at'    => $mentorshipReq->updated_at,
   ], 200);

  } catch (\Throwable $th) {
   return response()->json([
    'error'   => 'Something went wrong',
    'message' => $th->getMessage(),
   ], 500);
  }
 }

}
