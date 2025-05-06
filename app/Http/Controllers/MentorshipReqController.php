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
            $mentorshiprequests = MentorshipReq::paginate(10);
            return new MentorshipReqCollection($mentorshiprequests);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'something went wrong',
                'message' => $th->getMessage()
            ], 500);
        }
    }



    public function store(MentorshipReqRequest $request)
    {
        try {

            $validatedData = $request->validated();


            $mentorship = Mentorship::find($validatedData['mentorship_id']);
            if (!$mentorship) {
                return response()->json([
                    'error' => 'invalid mentorship session',
                    'message' => 'The mentorship session dose not exist'
                ], 404);
            }

            $isSessionBooked = MentorshipReq::where('mentorship_id', $validatedData['mentorship_id'])->exists();
            if ($isSessionBooked) {
                return response()->json([
                    'error' => 'Mentorship session already booked',
                    'message' => 'The selected mentorship session is already booked'
                ], 400);
            }


            //todo check avilable times
            $metorshipreq = MentorshipReq::create($validatedData);



            return new mentorshipReqResource($metorshipreq);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'something went wrong',
                'message' => $th->getMessage()
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
            $requests = MentorshipReq::where('mentee_id', $userId)->orWhere('mentor_id', $userId)->with(['user', 'mentor', 'mentorship'])->get();

            return response()->json($requests);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'error' => 'something went wrong',
                'message' => $th->getMessage()
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

    /* if(auth()->id() !== $mentorshipReq->mentor_id) {
        return  response()->json([
            'error'=>'Unauthorized'
        ],403);
    }

    $mentorshipReq->reschedule($request->date,$request->selectedtime);

    return response()->json([
        'error'=>'Mentorship request was successfully rescheduled',
        'mentorship request' => $mentorshipReq,
    ],200);

        } */

}
