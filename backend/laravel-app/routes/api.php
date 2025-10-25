<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('test', function () {
    return response()->json([
        'success' => true,
        'message' => 'hello api routes.'
    ]);
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('jwt.auth');
});

Route::prefix('profile')->controller(UserController::class)
    ->middleware('jwt.auth')
    ->group(function () {
        Route::get('', 'show');
        Route::put('', 'update');
    });

Route::apiResource('teams', TeamController::class)->middleware('jwt.auth');
Route::prefix('teams/{id}')->controller(TeamUserController::class)
    ->middleware('jwt.auth')
    ->group(function () {
        Route::post('members', 'store');
        Route::delete('members/{memberId}', 'destroy');
    });

Route::apiResource('projects', ProjectController::class)->middleware('jwt.auth');

Route::apiResource('projects.tasks', TaskController::class)->middleware('jwt.auth');

Route::apiResource('tasks.comments', CommentController::class)->shallow()
    ->except(['index', 'show'])
    ->middleware('jwt.auth');
