<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
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
    Route::post('logout', 'logout');
});

Route::prefix('profile')->controller(UserController::class)->group(function () {
    Route::get('', 'show');
    Route::put('update', 'update');
});

Route::apiResource('teams', TeamController::class);

Route::apiResource('projects', ProjectController::class);

Route::apiResource('projects.tasks', TaskController::class);

Route::apiResource('tasks.comments', CommentController::class)->shallow()
    ->except(['index', 'show']);
