<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMentorshipsRequest;
use App\Http\Resources\MentorshipReqCollection;
use App\Http\Resources\mentorshipReqResource;
use App\Models\Mentorship;
use App\Models\MentorshipReq;
use Database\Seeders\MentorshipreqSeeder;
use GuzzleHttp\Psr7\Message;
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

    public function store(StoreMentorshipsRequest $request){
try {

    $validatedData = $request->validated();


$mentorship = Mentorship::find($validatedData['mentorship_id']);
if(!$mentorship){
    return response()->json([
        'error'=>'invalid mentorship session',
        'message'=>'The mentorship session dose not exist'
    ], 404);
}
    //check if the session already booked

$isSessionBooked = MentorshipReq::where('mentorship_id', $validatedData['mentorship_id'])->exists();
if($isSessionBooked){
    return response()->json([
        'error'=>'Mentorship session already booked',
        'message'=>'The selected mentorship session is already booked'
    ], 400);
}

    $metorshipreq = MentorshipReq::create($validatedData);



    return new mentorshipReqResource($metorshipreq);

} catch (\Throwable $th) {
    return response()->json([
        'error'=> 'something went wrong',
        'message' => $th->getMessage()
    ],500);
}
    }


    public function getMentorRequests($userId){

        try {
            $requests=MentorshipReq::where('mentor_id', $userId)->with(['user', 'mentor', 'mentorship'])->get();

            return response()->json($requests);
                    } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'error'=> 'something went wrong',
                'message' => $th->getMessage()
            ],500);
        }

    }


    public function getMenteeRequests($userId){

        try {
            $requests=MentorshipReq::where('mentee_id', $userId)->orWhere('mentor_id', $userId)->with(['user', 'mentor', 'mentorship'])->get();

            return response()->json($requests);
                    } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'error'=> 'something went wrong',
                'message' => $th->getMessage()
            ],500);
        }

    }


    public function show($id)
    {
        // $user = User::find($id);

        // if (!$user) {
        //     return response()->json(['message' => 'User not found'], 404);
        // }

    return new mentorshipReqResource(MentorshipReq::findOrFail($id));

}


    public function getUserMentorsRequests(Request $request){
        try {

            $mentorId = Auth::id();
            $requests = MentorshipReq::where('mentor_Id' , $mentorId)->with(['mentee', 'mentorship'])->get();

            // return dd($mentorId);
            return response()->json($requests);

            //code...
        } catch (\Throwable $th) {

            return response()->json([
                'error'=>$th->getMessage()
            ],500);
        }
    }

}
