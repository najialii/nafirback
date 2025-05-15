<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\MentorshipReqController;
use App\Http\Controllers\ActivityReqController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ActivitiesLikesController;
use App\Http\Controllers\CVController;
use App\Http\Middleware\AuthOpt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\BlogLikesController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/user/me', [UserController::class, 'getMeData']);
Route::middleware('auth:sanctum')->patch('/user/edit', [UserController::class, 'update']);

Route::get('/user/{id}', [AuthController::class, 'show'])->middleware('auth:sanctum');
// Department
Route::get('/department/{id}', [DepartmentController::class, 'show']);
Route::get('/department', [DepartmentController::class, 'index']);
Route::post('/department', [DepartmentController::class, 'store'])->middleware('auth:sanctum');

// Activities
Route::get('/activities', [ActivityController::class, 'index'])->middleware(AuthOpt::class);
// Route::get('/activities/search/{keyword}', [ActivityController::class, 'searchActivity']);
// Route::get('/activities/{id}', [ActivityController::class, 'show']);
// Route::get('/activities/department/{id}', [ActivityController::class, 'departmentAct']);

Route::prefix('activities')->middleware('auth:sanctum')->group(function () {
    Route::controller(ActivityController::class)->group(function () {
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});

// Activity Requests
Route::post('/activity/{id}/request', [ActivityReqController::class, 'store'])->middleware('auth:sanctum');

// Mentorships
Route::get('/mentorships', [MentorshipController::class, 'index']);
Route::get('/mentorship/{id}', [MentorshipController::class, 'show']);
Route::get('/search/mentorship/{keyword}', [MentorshipController::class, 'searchMentorships']);

//Qu: Why store route is not protected?
Route::post('/mentorship', [MentorshipController::class, 'store']);

Route::prefix('mentorship')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [MentorshipController::class, 'store']); 
    Route::put('/{id}', [MentorshipController::class, 'update']); 
    Route::delete('/{id}', [MentorshipController::class, 'destroy']); 
});

// Mentorship Requests

// // mentee 
// Route::get('/mentee/mentorship', [MentorshipReqController::class, 'getMenteeRequests'])->middleware('auth:sanctum');
// Route::get('/mentorship/request/user/{id}', [MentorshipReqController::class, 'getOneMenteeRequest'])->middleware('auth:sanctum');
// Route::post('/mentorship/request', [MentorshipReqController::class, 'reqSession'])->middleware('auth:sanctum');
// Route::get('/mentee/entries', [MentorshipReqController::class, 'getAllMenteeEntries'])->middleware('auth:sanctum');
// Route::get('/mentee/entries/{id}', [MentorshipReqController::class, 'getOneMenteeEntry'])->middleware('auth:sanctum');
// Route::get('/mentorship/mentee/status', [MentorshipReqController::class, 'getMentorMentorshipStatuses'])->middleware('auth:sanctum');

// Route::get('/mentor/entries/{id}', [MentorshipReqController::class, 'getOneMentorEntryBy'])->middleware('auth:sanctum');

// //mentor
// Route::get('/mentorship/request/{id}', [MentorshipReqController::class, 'getoneMentorReq'])->middleware('auth:sanctum');
// Route::get('/mentorship/request/mentor', [MentorshipReqController::class, 'getAllMentorReq'])->middleware('auth:sanctum');
// Route::put('/mentorship/request/{id}/process', [MentorshipReqController::class, 'processMentorshipReq'])->middleware('auth:sanctum');
// Route::delete('/mentorship/request/{id}', [MentorshipReqController::class, 'destroy'])->middleware('auth:sanctum');Route::get('/mentorship/{id}/mentor', [MentorshipReqController::class, 'getMentorRequests'])->middleware('auth:sanctum');
// Route::get('/mentorship/mentor/status', [MentorshipReqController::class, 'getMenteeMentorshipStatuses'])->middleware('auth:sanctum');


// Route::get('/mentor/entries', [MentorshipReqController::class, 'getAllMentorEntries'])->middleware('auth:sanctum');
// Route::get('/mentor/entries/{id}', [MentorshipReqController::class, 'getAllMentorEntries'])->middleware('auth:sanctum');


//mentee status gets all teh user date includubng [mentorship sesssionns , ]
Route::get('/mentee/status', [MentorshipReqController::class, 'getUserStatus']);
Route::get('/mentee/mentorship/{id}/status', [MentorshipReqController::class, 'getUserMentorshipStatus']);

// mentee mentorship
//
Route::get('/mentee/mentorship', [MentorshipController::class, 'getMentorshipsByMentee']);
Route::get('/mentee/mentorship/{id}', [MentorshipController::class, 'getOneMentorshipForMentee'])->middleware('auth:sanctum');

// Mentee - Mentorship Requests
Route::get('/mentee/requests', [MentorshipReqController::class, 'getMenteeRequests'])->middleware('auth:sanctum'); // List mentee's requests
Route::get('/mentee/requests/{id}', [MentorshipReqController::class, 'getOneMenteeRequest'])->middleware('auth:sanctum'); // View single mentee request
Route::post('/mentorship/entry/{id}/request', [MentorshipReqController::class, 'reqSession'])->middleware('auth:sanctum'); // Create new request

// Mentee - Session Entries
Route::get('/mentee/sessions', [MentorshipReqController::class, 'getAllMenteeEntries'])->middleware('auth:sanctum'); // All sessions available to mentee
Route::get('/mentee/session/{id}', [MentorshipReqController::class, 'getOneMenteeEntry'])->middleware('auth:sanctum'); // Single session detail

// Mentee - Status overview (e.g. sessions booked, completed)
Route::get('/mentee/mentorship/status', [MentorshipReqController::class, 'getMenteeMentorshipStatuses'])->middleware('auth:sanctum'); 



//mentor mentorship
Route::get('/mentorships/mentor', [MentorshipController::class, 'getMentorshipsByMentor'])->middleware('auth:sanctum');
Route::get('/mentorships/mentor/{id}', [MentorshipController::class, 'getOneMentorshipForMentor'])->middleware('auth:sanctum');

// Mentor - Requests
Route::get('/mentor/requests', [MentorshipReqController::class, 'getMentorshipRequests'])->middleware('auth:sanctum'); // List all requests to mentor
Route::get('/mentor/requests/{id}', [MentorshipReqController::class, 'getMentorshipRequestById'])->middleware('auth:sanctum'); // View a specific mentorship request
Route::put('/mentor/requests/{id}/status', [MentorshipReqController::class, 'processMentorshipReq'])->middleware('auth:sanctum'); // Process (accept/reject)
Route::delete('/mentor/requests/{id}', [MentorshipReqController::class, 'destroy'])->middleware('auth:sanctum'); // Cancel/delete a request

// Mentor - Requests for a specific mentorship program
Route::get('/mentor/mentorships/{id}/requests', [MentorshipReqController::class, 'getMentorRequests'])->middleware('auth:sanctum');

// Mentor - Session Entries
Route::get('/mentor/sessions', [MentorshipReqController::class, 'getAllMentorEntries'])->middleware('auth:sanctum'); // List of all sessions
Route::get('/mentor/sessions/{id}', [MentorshipReqController::class, 'getOneMentorEntryBy'])->middleware('auth:sanctum'); // Details for one session

// Mentor - Status overview
Route::get('/mentor/mentorship/status', [MentorshipReqController::class, 'getMenteeMentorshipStatuses'])->middleware('auth:sanctum');




//post mentorship entries 
Route::middleware('auth:sanctum')->post('/mentorship-entries', [MentorshipController::class, 'store']);
























// Route::get('/mentorship/{id}/request', [MentorshipReqController::class, 'getMenteeRequests'])->middleware('auth:sanctum');
// Route::post('/mentorship/{id}/request', [MentorshipReqController::class, 'store'])->middleware('auth:sanctum');
// Route::delete('/mentorship/{id}/request', [MentorshipReqController::class, 'destroy'])->middleware('auth:sanctum');
// Route::patch('mentorship/{id}/request', [MentorshipController::class, 'processMentorshipRequest'])
//Route::put('/mentorship/request/{id}/status', [MentorshipReqController::class, 'processMentorshipRequest'])->middleware('auth:sanctum');

// Users/mentorship/{id}/request/{request_id}
Route::get('/user', [UserController::class, 'index']);
Route::get('/user/{id}', [UserController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user', [UserController::class, 'store']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    // Route::patch('/user/{id}', [UserController::class, 'update']);
});

// Blogs

Route::get('/posts', [BlogController::class, 'index'])->middleware(AuthOpt::class);
Route::get('/post/{id}', [BlogController::class, 'show'])->middleware('auth:sanctum');

Route::get('search/{keyword}', [BlogController::class, 'search']);
Route::get('/posts/department/{id}', [BlogController::class, 'departmentBlogs']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/post', [BlogController::class, 'store']);
    Route::put('/post/{id}', [BlogController::class, 'update']);
    Route::delete('/post/{id}', [BlogController::class, 'destroy']);
});



Route::post('/rate-cv', [CVController::class, 'rate']);
Route::post('/cv', [CVController::class, 'store']);
// Route::post('/create-cv', [CVController::class, 'store']);




// activit
Route::post('/activities/{id}/like', [ActivitiesLikesController::class, 'fav_activity'])->middleware('auth:sanctum');
Route::get('/favorite/activities', [ActivitiesLikesController::class, 'getfav_activites'])->middleware('auth:sanctum');
;
// blikes
Route::post('/post/{blogId}/like', [BlogLikesController::class, 'fav_blog'])->middleware('auth:sanctum');
Route::get('/favorite/blogs', [BlogLikesController::class, 'getfav_blogs'])->middleware('auth:sanctum');








Route::middleware('auth:sanctum')->group(function () {
 Route::post('/email/verification-notification', function (Request $request) {
  if ($request->user()->hasVerifiedEmail()) {
   return response()->json(['message' => 'Already verified']);
  }

  $request->user()->sendEmailVerificationNotification();

  return response()->json(['message' => 'Verification link sent!']);
 });

 Route::get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
  $request->fulfill();

  return response()->json(['message' => 'Email verified!']);
 })->middleware(['signed'])->name('verification.verify');
});

