<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\MentorshipReqController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//reg
Route::post('/register', [AuthController::class, 'register']);

//auth

Route::post('/login',[AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::post('/department', [DepartmentController::class, 'store']);
Route::get('/department', [DepartmentController::class, 'index']);

Route::get('/department/{id}', [DepartmentController::class, 'show']);

//Activites
Route::get('/activites', [ActivityController::class, 'index']);
Route::post('/activites', [ActivityController::class, 'store']);
Route::get('/activites/{id}', [ActivityController::class, 'show']);


//mentorships
Route::get('/mentorships', [MentorshipController::class, 'index']);
Route::get('/mentorship/{id}', [MentorshipController::class, 'show']);
Route::post('/mentorship', [MentorshipController::class, 'store']);


Route::get('/mentorshiprequest', [MentorshipReqController::class, 'index']);
Route::get('/mentorshiprequest/{id}', [MentorshipReqController::class, 'getMentorMentorsRequests']);
Route::put('/mentorshiprequest/{id}/status', [MentorshipReqController::class, 'getMentorMentorsRequests']);
// Route::get('/mentorshiprequest/{id}', [MentorshipReqController::class, 'show']);

Route::post('/request_session', [MentorshipReqController::class, 'store']);
// Route::get('/mentorshiprequest', [MentorshipReqController::class, 'getUserMentorsRequests']);
// ->middleware('auth:sanctum');
// ->middleware('auth:sanctum');
// ->middleware('auth:sanctum');



// ->middleware('role:admin');

//users
Route::post('/test',[TestController::class,'index']);
Route::get('/user',[UserController::class,'index']);
Route::get('/user/{id}', [UserController::class, 'show']);

Route::post('/user', [UserController::class, 'store']);

Route::put('/user/{id}', [UserController::class, 'update']);
Route::patch('/user/{id}', [UserController::class, 'update']);




//departments


// Route::get('/department', [DepartmentController::class, '']);


// Route::get('/user',[UserController::class,'index']);







//Mentorship sessions







//Activites







//Blogs



