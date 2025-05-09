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


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/sauth', [AuthController::class, 'sauth']);

// Department
Route::get('/department', [DepartmentController::class, 'index']);
Route::get('/department/{id}', [DepartmentController::class, 'show']);
Route::post('/department', [DepartmentController::class, 'store'])->middleware('auth:sanctum');

// Activities
Route::get('/activites', [ActivityController::class, 'index']);
Route::get('/activities/{id}', [ActivityController::class, 'show']);

Route::prefix('activity')->middleware('auth:sanctum')->group(function () {
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

Route::prefix('mentorship')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [MentorshipController::class, 'store']);
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
Route::get('/user/{id}', [UserController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user', [UserController::class, 'store']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::patch('/user/{id}', [UserController::class, 'update']);
});

// Blogs
Route::get('/post', [BlogController::class, 'index']);
Route::get('/post/{id}', [BlogController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/post', [BlogController::class, 'store']);
    Route::put('/post/{id}', [BlogController::class, 'update']);
    Route::delete('/post/{id}', [BlogController::class, 'destroy']);
});



