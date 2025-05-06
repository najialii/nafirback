<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserController,
    DepartmentController,
    ActivityController,
    ActivityReqController,
    ActivitiesLikesController,
    MentorshipController,
    MentorshipReqController,
    BlogController,
    BlogLikesController,
    CVController
};

// Auth
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/auth/me', [AuthController::class, 'getMeData'])->middleware('auth:sanctum');
Route::post('/auth/sauth', [AuthController::class, 'sauth']);
Route::post('/auth/atauth', [AuthController::class, 'atauth']);

// Departments
Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/departments/{id}', [DepartmentController::class, 'show']);
Route::post('/departments', [DepartmentController::class, 'store'])->middleware('auth:sanctum');

// Activities
Route::get('/activities', [ActivityController::class, 'index']);
Route::get('/activities/search/{keyword}', [ActivityController::class, 'searchActivity']);
Route::get('/activities/{id}', [ActivityController::class, 'show']);
Route::get('/departments/{id}/activities', [ActivityController::class, 'departmentAct']);

Route::middleware('auth:sanctum')->prefix('activities')->group(function () {
    Route::post('/', [ActivityController::class, 'store']);
    Route::put('/{id}', [ActivityController::class, 'update']);
    Route::delete('/{id}', [ActivityController::class, 'destroy']);
    Route::post('/{id}/like', [ActivitiesLikesController::class, 'toggle']);
});

// Activity Requests
Route::post('/activity-requests', [ActivityReqController::class, 'store'])->middleware('auth:sanctum');

// Mentorships
Route::get('/mentorships', [MentorshipController::class, 'index']);
Route::get('/mentorships/search/{keyword}', [MentorshipController::class, 'searchMentorships']);
Route::get('/mentorships/{id}', [MentorshipController::class, 'show']);
Route::post('/mentorships', [MentorshipController::class, 'store']);

Route::middleware('auth:sanctum')->prefix('mentorships')->group(function () {
    Route::put('/{id}', [MentorshipController::class, 'update']);
    Route::delete('/{id}', [MentorshipController::class, 'destroy']);
});

// Mentorship Requests
Route::get('/mentorship-requests', [MentorshipReqController::class, 'index']);
Route::get('/mentorship-requests/{id}', [MentorshipReqController::class, 'show']);
Route::put('/mentorship-requests/{id}/status', [MentorshipReqController::class, 'processMentorshipRequest'])->middleware('auth:sanctum');
Route::post('/mentorship-requests', [MentorshipReqController::class, 'store'])->middleware('auth:sanctum');

// Users
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/users', [UserController::class, 'store']);
    Route::match(['put', 'patch'], '/users/{id}', [UserController::class, 'update']);
});

// Blogs
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{id}', [BlogController::class, 'show']);
Route::get('/blogs/search/{keyword}', [BlogController::class, 'search']);
Route::get('/departments/{id}/blogs', [BlogController::class, 'departmentBlogs']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{id}', [BlogController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
    Route::post('/blogs/{blogId}/like', [BlogLikesController::class, 'toggle']);
});

// CVs
Route::post('/cvs', [CVController::class, 'store']);
Route::post('/cvs/rate', [CVController::class, 'rate']);
