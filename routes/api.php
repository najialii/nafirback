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
use App\Http\Controllers\BlogLikesController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/user/me', [UserController::class, 'getMeData']);
Route::get('/user/{id}' ,[AuthController::class, 'show'])->middleware('auth:sanctum');
// Department
Route::get('/department/{id}', [DepartmentController::class, 'show']);
Route::get('/department', [DepartmentController::class, 'index']);
Route::post('/department', [DepartmentController::class, 'store'])->middleware('auth:sanctum');

// Activities
Route::get('/activities', [ActivityController::class, 'index'])->middleware(AuthOpt::class);
Route::get('/activities/search/{keyword}', [ActivityController::class, 'searchActivity']);
Route::get('/activities/{id}', [ActivityController::class, 'show']);
Route::get('/activities/department/{id}', [ActivityController::class, 'departmentAct']);

Route::prefix('activities')->middleware('auth:sanctum')->group(function () {
    Route::controller(ActivityController::class)->group(function () {
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});

// Activity Requests
Route::post('/activity-requests', [ActivityReqController::class, 'store'])->middleware('auth:sanctum');

// Mentorships
Route::get('/mentorships', [MentorshipController::class, 'index']);
Route::get('/mentorship/{id}', [MentorshipController::class, 'show']);
Route::get('/search/mentorship/{keyword}', [MentorshipController::class, 'searchMentorships']);
Route::post('/mentorship', [MentorshipController::class, 'store']);

Route::prefix('mentorship')->middleware('auth:sanctum')->group(function () {
    // Route::post('/', [MentorshipController::class, 'store']);
    Route::put('/{id}', [MentorshipController::class, 'update']);
    Route::delete('/{id}', [MentorshipController::class, 'destroy']);
});

// Mentorship Requests
Route::get('/mentorshiprequest', [MentorshipReqController::class, 'index']);
Route::get('/mentorshiprequest/{id}', [MentorshipReqController::class, 'show']);
Route::put('/mentorshiprequest/{id}/status', [MentorshipReqController::class, 'processMentorshipRequest'])->middleware('auth:sanctum');
Route::post('/request_session', [MentorshipReqController::class, 'store'])->middleware('auth:sanctum');

// Users
Route::get('/user', [UserController::class, 'index']);
// Route::get('/user/{id}', [UserController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user', [UserController::class, 'store']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::patch('/user/{id}', [UserController::class, 'update']);
});

// Blogs

Route::get('/posts', [BlogController::class, 'index'])->middleware(AuthOpt::class);
Route::get('/post/{id}', [BlogController::class, 'show']);

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
Route::get('/favorite/activites', [ActivitiesLikesController::class, 'getfav_activites'])->middleware('auth:sanctum');;
// blikes
Route::post('/post/{blogId}/like', [BlogLikesController::class, 'toggle'])->middleware('auth:sanctum');