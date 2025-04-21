<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mentorship;
use App\Http\Resources\MentorshipCollection;
use App\Http\Resources\MentorshipResource;
use App\Http\Requests\StoreMentorshipsRequest;
use App\Http\Requests\MentorshipUpdateRequest;

class MentorshipController extends Controller
{
    //

  public function index(){
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


  public function show($id){
    try {
        return new MentorshipResource(Mentorship::findOrFail($id));


    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Something went wrong!',
            'message' => $th->getMessage()
        ], 500);
    }
  }



  public function store(StoreMentorshipsRequest $request){
    try{
        $validatedData = $request->validated();

        $validatedData['days'] = json_encode($validatedData['days']);
        $validatedData['available_times'] = json_encode($validatedData['available_times']);
        $mentorship = Mentorship::create($validatedData);
        return new MentorshipResource($mentorship);
    } catch(\Throwable $th){
        return response()->json([
            'error'=> 'something went wrong!',
            'message'=> $th->getMessage()
        ],500);
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
              'data' => new MentorshipResource($mentorship)
          ]);

      } catch (\Throwable $th) {
          return response()->json([
              'error' => 'Something went wrong',
              'message' => $th->getMessage()
          ], 500);
      }
  }


  public function destroy(string $id)
  {
      try {
          $mentorship = Mentorship::findOrFail($id);
          $mentorship->delete();

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
