<?php
namespace App\Http\Controllers;

use App\Http\Resources\MenteeEntryResource;
use App\Http\Resources\MentorshipEntryResource;
use App\Models\Mentorship;
use App\Models\MentorshipEntry;
use App\Models\MentorshipReq;
use function Pest\Laravel\json;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MentorshipReqController extends Controller
{
 //



 private function bookedRecently($menteeId, $days = 30): bool
 {
     return MentorshipReq::where('mentee_id', $menteeId)
         ->where('status', 'accepted') // Only check for accepted sessions
         ->where('created_at', '>=', now()->subDays($days)) // Within the last X days
         ->exists();
 }

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
    $user = auth()->user();

    try {
        $mentorshipReqs = MentorshipReq::whereHas('mentorship_entry.mentorship', function ($query) use ($user) {
                $query->where('mentor_id', $user->id); // only requests to mentor's mentorships
            })
            ->with(['mentee', 'mentorship_entry.mentorship']) // load relationships
            ->select('id', 'mentee_id', 'mentorship_entry_id', 'message', 'status', 'created_at', 'updated_at')
            ->get();

        $formatted = $mentorshipReqs->map(function ($req) {
            return [
                'id' => $req->id,
                'mentorship_entry_id' => $req->mentorship_entry_id,
                'status' => $req->status,
                'mentee' => [
                    'id' => $req->mentee->id,
                    'img' => $req->mentee->profile_pic,
                    'name' => $req->mentee->name,
                ],
                'mentorship' => [
                    'id' => $req->mentorship_entry->mentorship->id,
                    'name' => $req->mentorship_entry->mentorship->name ?? null,
                ],
                'message' => $req->message,
                'created_at' => $req->created_at,
                'updated_at' => $req->updated_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formatted,
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Something went wrong',
            'message' => $th->getMessage(),
        ], 500);
    }
}




public function getMentorshipRequests()
{
    $user = auth()->user(); 

    try {
        $mentorshipRequests = MentorshipReq::whereHas('mentorship_entry.mentorship', function ($query) use ($user) {
            $query->where('mentor_id', $user->id);
        })
        ->with([
            'mentee:id,name,profile_pic', 
            'mentorship_entry.mentorship:id,name,mentor_id',
        ])
        ->get();

        $mappedRequests = $mentorshipRequests->map(function ($request) {
            return [
                'id' => (string) $request->id,
                'mentee' => [
                    'id' => $request->mentee->id,
                    'name' => $request->mentee->name,
                    'img' => $request->mentee->profile_pic,
                ],
                'status' => $request->status,
                'requested_entry' => [
                    'id' => $request->mentorship_entry_id,
                    'start_date' => $request->mentorship_entry->start_date,
                    'duration' => $request->mentorship_entry->duration,
                    'mentorship' => [
                        'id' => $request->mentorship_entry->mentorship->id,
                        'name' => $request->mentorship_entry->mentorship->name,
                    ],
                    'available' => $request->mentorship_entry->status !== 'booked-out', 
                ],
                'message' => $request->message,
                'created_at' => $request->created_at->toIso8601String(),
                'updated_at' => $request->updated_at->toIso8601String(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $mappedRequests,
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => 'error',
            'message' => $th->getMessage(),
        ], 500);
    }
}



public function getMentorshipRequestById($id)
{
    $user = auth()->user(); 

    try {
        $mentorshipRequest = MentorshipReq::where('id', $id)
            ->whereHas('mentorship_entry.mentorship', function ($query) use ($user) {
                $query->where('mentor_id', $user->id);
            })
            ->with([
                'mentee:id,name,profile_pic',
                'mentorship_entry.mentorship:id,name,mentor_id',
            ])
            ->firstOrFail(); 

        $data = [
            'id' => (string) $mentorshipRequest->id,
            'mentee' => [
                'id' => $mentorshipRequest->mentee->id,
                'name' => $mentorshipRequest->mentee->name,
                'img' => $mentorshipRequest->mentee->profile_pic,
            ],
            'status' => $mentorshipRequest->status,
            'requested_entry' => [
                'id' => $mentorshipRequest->mentorship_entry_id,
                'start_date' => $mentorshipRequest->mentorship_entry->start_date,
                'duration' => $mentorshipRequest->mentorship_entry->duration,
                'mentorship' => [
                    'id' => $mentorshipRequest->mentorship_entry->mentorship->id,
                    'name' => $mentorshipRequest->mentorship_entry->mentorship->name,
                ],
                'available' => $mentorshipRequest->mentorship_entry->status !== 'booked-out',
            ],
            'message' => $mentorshipRequest->message,
            'created_at' => $mentorshipRequest->created_at->toIso8601String(),
            'updated_at' => $mentorshipRequest->updated_at->toIso8601String(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Mentorship request not found or access denied.',
        ], 404);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => 'error',
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
 
         if ($mentorshipEntries->isEmpty()) {
             return response()->json([
                 'message' => 'No mentorship entries found for this mentee.',
             ], 404);
         }
 
         $requests = MentorshipReq::whereIn('mentorship_entry_id', $mentorshipEntries->pluck('id'))
             ->with(['mentee', 'mentorship_entry'])
             ->get();
 
         if ($requests->isEmpty()) {
             return response()->json([
                 'message' => 'No mentorship requests found.',
             ], 404);
         }
 
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
 


 public function reqSession(Request $request, $mentorship_entry_id)
 {
     $user = Auth::user();
 
     try {
         $validatedData = [
             'mentorship_entry_id' => $mentorship_entry_id,
             'message' => $request->input('message'), 
         ];
 
         $mentorshipEntry = MentorshipEntry::find($validatedData['mentorship_entry_id']);
 
         if (!$mentorshipEntry) {
             throw new \Exception('The mentorship entry does not exist');
         }
 
         if ($this->bookedRecently($user->id)) {
             return response()->json([
                 'error' => 'You cannot book a session.',
                 'message' => 'You have already booked a session in the last 30 days.',
             ], 400);
         }
 
         if ($mentorshipEntry->mentorship->mentor_id === $user->id) {
             throw new \Exception('Mentors cannot book their own sessions.');
         }
 
         $mentorshipReq = MentorshipReq::create([
             'mentorship_entry_id' => $validatedData['mentorship_entry_id'],
             'mentor_id' => $mentorshipEntry->mentorship->mentor_id,
             'mentee_id' => $user->id,
             'message' => $validatedData['message'] ?? null,
             'status' => 'pending',
             'session_date' => $mentorshipEntry->session_date,
         ]);
 
         $mentorshipReq->load(['mentee', 'mentorship_entry.mentorship.mentor']);
 
         return response()->json([
             'message' => 'Mentorship request created successfully.',
             'data' => [
                //  'id' => $mentorshipReq->id,
                //  'mentorship_entry_id' => $mentorshipReq->mentorship_entry_id,
                //  'mentee' => [
                //      'id' => $mentorshipReq->mentee->id,
                //      'name' => $mentorshipReq->mentee->name,
                //  ],
                //  'mentor' => [
                //      'id' => $mentorshipReq->mentorship_entry->mentorship->mentor->id,
                //      'name' => $mentorshipReq->mentorship_entry->mentorship->mentor->name,
                //      'session_date' => $mentorshipReq->session_date,
                //  ],
                //  'message' => $mentorshipReq->message,
                 'status' => $mentorshipReq->status,
             ],
         ], 201);
 
     } catch (\Exception $e) {
         return response()->json([
             'error' => 'Failed to create mentorship request.',
             'message' => $e->getMessage(),
         ], 400);
     }
 }
  
 public function processMentorshipReq(Request $request, $id)
 {
     $user = Auth::user();
 
     $validatedData = $request->validate([
         'status' => ['required', 'string', 'in:pending,accepted,rejected'],
     ]);
 
     $mentorshipRequest = MentorshipReq::with(['mentee', 'mentorship_entry.mentorship.mentor'])->find($id);
 
     if (!$mentorshipRequest) {
         return response()->json(['error' => 'Mentorship request not found.'], 404);
     }
 
     $mentorshipEntry = $mentorshipRequest->mentorship_entry;
     if (!$mentorshipEntry) {
         return response()->json(['error' => 'Mentorship entry not found.'], 404);
     }
 
     if ($mentorshipEntry->mentorship->mentor_id !== $user->id) {
         return response()->json(['error' => 'Unauthorized to process this request.'], 403);
     }
 
     try {
         DB::transaction(function () use ($mentorshipRequest, $mentorshipEntry, $validatedData) {
             $mentorshipRequest->status = $validatedData['status'];
             $mentorshipRequest->save();
 
             if (in_array($validatedData['status'], ['accepted', 'rejected'])) {
                 $mentorshipEntry->status = $validatedData['status'];
                 $mentorshipEntry->save();
             }
         });
 
         return response()->json([
             'message' => 'Your mentorship session has been updated successfully!',
            //  'data' => [
            //      'id'                  => $mentorshipRequest->id,
            //      'mentorship_entry_id' => $mentorshipRequest->mentorship_entry_id,
            //      'status'              => $mentorshipRequest->status,
            //      'mentee'              => [
            //          'id'   => $mentorshipRequest->mentee->id,
            //          'name' => $mentorshipRequest->mentee->name,
            //      ],
            //      'mentor' => [
            //          'id'   => $mentorshipRequest->mentorship_entry->mentorship->mentor->id,
            //          'name' => $mentorshipRequest->mentorship_entry->mentorship->mentor->name,
            //      ],
            //  ],
         ], 200);
 
     } catch (\Exception $e) {
         return response()->json([
             'error'   => 'Failed to process mentorship request.',
             'message' => $e->getMessage(),
         ], 500);
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

   $requests = MentorshipReq::where('mentor_id', $userId)->with(['user', 'mentor', 'mentorship'])->get();

   return response()->json($requests);

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



 public function getAllMentorEntries()
 {
     $mentorId = auth()->id();
 
     try {
         $mentorshipEntries = MentorshipEntry::whereHas('mentorship', function ($query) use ($mentorId) {
             $query->where('mentor_id', $mentorId);
         })->with(['mentorship', 'mentorship.mentor', 'requests'])->get();
 
         $entriesResource = $mentorshipEntries->map(function ($entry) {
             return new MentorshipEntryResource($entry, 'mentor');
         });
 
         return response()->json([
             'message' => 'Mentorship entries retrieved successfully.',
             'data' => $entriesResource,
         ], 200);
     } catch (\Throwable $th) {
         return response()->json([
             'error' => 'Something went wrong.',
             'message' => $th->getMessage(),
         ], 500);
     }
 }


public function getOneMentorEntryBy($id)
{
    $mentorId = auth()->id();

    try {
        $mentorshipEntry = MentorshipEntry::whereHas('mentorship', function ($query) use ($mentorId) {
            $query->where('mentor_id', $mentorId);
        })->with(['mentorship', 'mentorship.mentor', 'requests'])->findOrFail($id);

        return response()->json([
            'message' => 'Mentorship entry retrieved successfully.',
            'data' => new MentorshipEntryResource($mentorshipEntry, 'mentor'),
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Something went wrong.',
            'message' => $th->getMessage(),
        ], 500);
    }
}



public function getAllMenteeEntries()
{
    $menteeId = auth()->id();

    try {
        $mentorshipEntries = MentorshipEntry::whereHas('request', function ($query) use ($menteeId) {
            $query->where('mentee_id', $menteeId);
        })->with(['mentorship', 'mentorship.mentor', 'requests.mentee'])->get(); 

        $entriesResource = $mentorshipEntries->map(function ($entry) {
            return new MenteeEntryResource($entry);
        });

        return response()->json([
            'message' => 'mentee Mentorship entries retrieved successfully.',
            'data' => $entriesResource,
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Something went wrong.',
            'message' => $th->getMessage(),
        ], 500);
    }
}





public function getOneMenteeEntry($id)
{
    $menteeId = auth()->id();

    try {
        $mentorshipEntry = MentorshipEntry::whereHas('request', function ($query) use ($menteeId) {
            $query->where('mentee_id', $menteeId);
        })
        ->with(['mentorship', 'mentorship.mentor', 'requests'])
        ->find($id);  

        if (!$mentorshipEntry) {
            return response()->json([
                'error' => 'Mentorship entry not found.',
                'message' => 'No mentorship entry found with the given ID.',
            ], 404);
        }

        return response()->json([
            'message' => 'Mentorship entry retrieved successfully.',
            'data' => new MenteeEntryResource($mentorshipEntry),
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Something went wrong.',
            'message' => $th->getMessage(),
        ], 500);
    }
}




public function getMeMentorshipStatuses()
{
    $userId = auth()->id();

    try {
        $asMentor = Mentorship::with(['entries.request.mentee'])
            ->where('mentor_id', $userId)
            ->get()
            ->map(function ($mentorship) {
                $entry = $mentorship->entries->whereNotNull('accepted_request_id')->first();
                $mentee = optional($entry?->request?->mentee);

                return [
                    'status' => $entry?->status ?? 'no entry',
                    'other_user' => $mentee ? [
                        'id' => $mentee->id,
                        'name' => $mentee->name,
                        'email' => $mentee->email,
                    ] : null,
                ];
            })->filter(fn($entry) => $entry['other_user']);

        return response()->json([
            'message' => 'Mentorship statuses as mentor retrieved successfully.',
            'data' => $asMentor->values(),
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Failed to retrieve mentor statuses.',
            'message' => $th->getMessage(),
        ], 500);
    }
}




public function  getUserStatus()
{
    
    try {
        $user = Auth()->user();
        if(!$user)
        {
            return response()->json([
                'error' => 'user is is missing'
            ]);
        }
        
        $entries = MentorshipEntry::with(['request', 'mentorship'])
        ->whereHas('request', function ($q) use ($user) {
            $q->where('mentee_id', $user->id);
        })
        ->get();

    $data = $entries->map(function ($entry) {
        return [
            'entry_id' => $entry->id,
            'start_date' => $entry->start_date,
            'duration' => $entry->duration,
            'status' => $entry->status,
            'can_book' => true,
            'mentorship' => [
                'id' => $entry->mentorship->id ?? null,
                'name' => $entry->mentorship->name ?? null,
            ],
            'request' => $entry->request ? [
                'id' => $entry->request->id,
                'message' => $entry->request->message,
                'status' => $entry->request->status,
            ] : null,
        ];
    });

    return response()->json([
        'message' => 'Mentee status retrieved successfully',
        'data' => $data,
    ]);

    } catch (\Throwable $th) {
        return response()->json([
            'message'=> 'Mrror getting user status, please try again later'
            ,'error'=> $th->getMessage()
        ]);
    }
}


public function getUserMentorshipStatus($mentorshipId)
{
    try {
        $user = Auth::user();

        $entries = MentorshipEntry::with(['mentorship'])
            ->where('mentorship_id', $mentorshipId)
            ->whereHas('request', function ($q) use ($user) {
                $q->where('mentee_id', $user->id);
            })
            ->get();

        $data = $entries->map(function ($entry) {
            return [
                'entry_id' => $entry->id,
                'start_date' => $entry->start_date,
                'duration' => $entry->duration,
                'status' => $entry->status,
                'request' => $entry->request ? [
                    'id' => $entry->request->id,
                    'message' => $entry->request->message,
                    'status' => $entry->request->status,
                ] : null,
            ];
        });

        return response()->json([
            'message' => 'Booked entries for this mentorship retrieved successfully',
            'data' => $data,
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Failed to retrieve mentee mentorship status',
            'message' => $th->getMessage(),
        ], 500);
    }
}



public function getMenteeMentorshipStatuses()
{
    $userId = auth()->id();

    try {
        $asMentee = MentorshipReq::with(['mentorship_entry.mentorship.mentor'])
            ->where('mentee_id', $userId)
            ->whereNotNull('mentorship_entry_id')
            ->get()
            ->map(function ($req) {
                $entry = $req->mentorship_entry;
                $mentor = optional($entry?->mentorship?->mentor);

                return [
                    'status' => $entry?->status ?? 'no entry',
                    'other_user' => $mentor ? [
                        'id' => $mentor->id,
                        'name' => $mentor->name,
                        'email' => $mentor->email,
                    ] : null,
                ];
            })->filter(fn($entry) => $entry['other_user']);

        return response()->json([
            'message' => 'Mentorship statuses as mentee retrieved successfully.',
            'data' => $asMentee->values(),
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Failed to retrieve mentee statuses.',
            'message' => $th->getMessage(),
        ], 500);
    }
}



}
