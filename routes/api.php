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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//auth

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::post('/department', [DepartmentController::class, 'store']);
Route::get('/department', [DepartmentController::class, 'index']);

Route::get('/department/{id}', [DepartmentController::class, 'show']);

//Activites
Route::get('/activites', [ActivityController::class, 'index']);
Route::post('/activites', [ActivityController::class, 'store']);
Route::put('/activites/{id}', [ActivityController::class, 'update']);
Route::delete('/activites/{id}', [ActivityController::class, 'destroy']);
Route::get('/activites/{id}', [ActivityController::class, 'show']);
Route::post('/activity-requests', [ActivityReqController::class, 'store']);




//mentorships
Route::get('/mentorships', [MentorshipController::class, 'index']);
Route::get('/mentorship/{id}', [MentorshipController::class, 'show']);
Route::post('/mentorship', [MentorshipController::class, 'store']);
Route::put('/mentorship/{id}', [MentorshipController::class, 'update']);
Route::delete('/mentorship/{id}', [MentorshipController::class, 'destroy']);
// Route::post('/mentorship', [MentorshipController::class, 'store']);
// Route::post('/mentorship', [MentorshipController::class, 'store']);


Route::get('/mentorshiprequest', [MentorshipReqController::class, 'index']);
Route::get('/mentorshiprequest/{id}', [MentorshipReqController::class, 'show']);
Route::put('/mentorshiprequest/{id}/status', [MentorshipReqController::class, 'processMentorshipRequest']);
Route::post('/request_session', [MentorshipReqController::class, 'store']);




// ->middleware('role:admin');

//users
Route::get('/user',[UserController::class,'index']);
Route::get('/user/{id}', [UserController::class, 'show']);


Route::post('/user', [UserController::class, 'store']);
Route::put('/user/{id}', [UserController::class, 'update']);
Route::patch('/user/{id}', [UserController::class, 'update']);


Route::get('/posts', [BlogController::class, 'index']);
Route::post('/post', [BlogController::class, 'store']);
Route::get('/post/{id}', [BlogController::class, 'show']);
Route::put('/post/{id}', [BlogController::class, 'update']);
Route::delete('/post/{id}', [BlogController::class, 'destroy']);





//departments


// Route::get('/department', [DepartmentController::class, '']);


// Route::get('/user',[UserController::class,'index']);







//Mentorship sessions







//Activites







//Blogs



