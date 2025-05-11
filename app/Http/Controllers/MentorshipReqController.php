<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMentorshipsRequest;
use App\Http\Requests\MentorshipReqRequest;
use App\Http\Resources\MentorshipReqCollection;
use App\Http\Resources\mentorshipReqResource;
use App\Models\Mentorship;
use App\Models\MentorshipReq;
use Database\Seeders\MentorshipreqSeeder;
use GuzzleHttp\Psr7\Message;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\json;

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
                'error' => 'Something went wrong',
                'message' => $th->getMessage(),
            ], 500);
        }
    }



    
 
public function store(Request $request, $id)
{
    $user = auth()->user();

    try {
        $validatedData = $request->validate([
            'message' => 'nullable|string',
            'sele_date' => 'required|date',
            'sele_time' => 'required|date_format:H:i',
        ]);

        $mentorship = Mentorship::find($id);
        if (!$mentorship) {
            return response()->json([
                'error' => 'Invalid mentorship session',
                'message' => 'The mentorship session does not exist',
            ], 404);
        }

        $isBooked = MentorshipReq::where('mentorship_id', $id)
            ->where('mentee_id', $user->id)
            ->exists();

        if ($isBooked) {
            return response()->json([
                'error' => 'Mentorship session already booked',
                'message' => 'You have already booked this mentorship session',
            ], 400);
        }

        $mentorshipReq = MentorshipReq::create([
            'mentorship_id' => $id,
            'mentor_id' => $mentorship->mentor_id,
            'mentee_id' => $user->id,
            'message' => $validatedData['message'] ?? null,
            'sele_date' => $validatedData['sele_date'],
            'sele_time' => $validatedData['sele_time'],
        ]);

        $mentorshipReq->load(['mentee', 'mentor', 'mentorship']);

        return response()->json([
            'id' => $mentorshipReq->id,
            'mentorship_id' => $mentorshipReq->mentorship_id,
            'sele_date' => $mentorshipReq->sele_date,
            'sele_time' => $mentorshipReq->sele_time,
            'message' => $mentorshipReq->message,
            'status' => $mentorshipReq->status,
            'created_at' => $mentorshipReq->created_at,
            'updated_at' => $mentorshipReq->updated_at,
            'mentee' => [
                'id' => $mentorshipReq->mentee->id,
                'name' => $mentorshipReq->mentee->name,
                'profile_pic' => $mentorshipReq->mentee->profile_pic,
            ],
            'mentor' => [
                'id' => $mentorshipReq->mentor->id,
                'name' => $mentorshipReq->mentor->name,
                'profile_pic' => $mentorshipReq->mentor->profile_pic,
            ],
        ], 201);
    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Something went wrong',
            'message' => $th->getMessage(),
        ], 500);
    }
}

    public function getMentorRequests($userId)
    {

        try {
            $requests = MentorshipReq::where('mentor_id', $userId)->with(['user', 'mentor', 'mentorship'])->get();

            return response()->json($requests);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'error' => 'something went wrong',
                'message' => $th->getMessage()
            ], 500);
        }

    }


 
    public function getMenteeRequests($userId)
    {
        try {
            $requests = MentorshipReq::where('mentee_id', $userId)
                ->orWhere('mentor_id', $userId)
                ->with(['mentee', 'mentor', 'mentorship'])
                ->select('id', 'mentorship_id', 'mentor_id', 'mentee_id', 'sele_date', 'sele_time', 'message', 'status', 'created_at', 'updated_at')
                ->get();
    
            return response()->json($requests, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $th->getMessage(),
            ], 500);
        }
    }




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
                'error' => $th->getMessage()
            ], 500);
        }
    }



    public function processMentorshipRequest(Request $request, $id)
    {
        try {

            $request->validate([
                'status' => ['required', 'string', 'in:pending,accepted,rejected'],
            ]);

            $mentorshipRequest = MentorshipReq::find($id);

            if (!$mentorshipRequest) {
                return response()->json([
                    'error' => 'Mentorship request not found',
                    'message' => 'The specified mentorship request does not exist'
                ], 404);
            }

            $mentorshipRequest->status = $request->status;
            $mentorshipRequest->save();

            return response()->json([
                'message' => 'Mentorship request status updated successfully',
                'mentorship_request' => $mentorshipRequest
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $th->getMessage()
            ], 500);
        }
    }



    public function session_reschedule(Request $request, $id)
    {
        $request->validate([
            'selectedDay' => 'required|date',
            'selectedtime' => 'required|date_format:H:i',
        ]);

        $mentorshipReq = MentorshipReq::find($id);

        if (!$mentorshipReq) {
            return response()->json([
                'error' => 'Mentorship request not found',
            ], 404);
        }

        if (auth()->id() !== $mentorshipReq->mentor_id) {
            return response()->json([
                'error' => 'Unauthorized'
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
                ->findOrFail($id);
    
            return response()->json([
                'id' => $mentorshipReq->id,
                'mentorship_id' => $mentorshipReq->mentorship_id,
                'sele_date' => $mentorshipReq->sele_date,
                'sele_time' => $mentorshipReq->sele_time,
                'message' => $mentorshipReq->message,
                'status' => $mentorshipReq->status,
                'created_at' => $mentorshipReq->created_at,
                'updated_at' => $mentorshipReq->updated_at,
                'mentee' => [
                    'id' => $mentorshipReq->mentee->id,
                    'name' => $mentorshipReq->mentee->name,
                    'profile_pic' => $mentorshipReq->mentee->profile_pic,
                ],
                'mentor' => [
                    'id' => $mentorshipReq->mentor->id,
                    'name' => $mentorshipReq->mentor->name,
                    'profile_pic' => $mentorshipReq->mentor->profile_pic,
                ],
                // 'mentorship' => [
                //     'id' => $mentorshipReq->mentorship->id,
                //     'name' => $mentorshipReq->mentorship->name,
                //     'date' => $mentorshipReq->mentorship->date,
                //     'sele_time' => $mentorshipReq->mentorship->sele_time,
                // ],
            ], 200);
    
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
    
}