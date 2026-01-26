<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectUserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamUserController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\JWTAuthenticate;
use Illuminate\Support\Facades\Route;

Route::get('test', function () {
    return response()->json([
        'success' => true,
        'message' => 'hello api routes.'
    ]);
})->withoutMiddleware(JWTAuthenticate::class);

Route::controller(AuthController::class)->group(function () {
    Route::withoutMiddleware(JWTAuthenticate::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
    });
    Route::post('logout', 'logout');
});

Route::prefix('profile')->controller(UserController::class)
    ->group(function () {
        Route::get('', 'show');
        Route::put('', 'update');
    });

Route::apiResource('teams', TeamController::class);
Route::prefix('teams/{team}')->controller(TeamUserController::class)
    ->group(function () {
        Route::post('members', 'store');
        Route::delete('members/{user_id}', 'destroy');
    });

Route::apiResource('projects', ProjectController::class);
Route::prefix('projects/{project}')
    ->controller(ProjectUserController::class)
    ->group(function () {
        Route::post('members', 'store');
        Route::delete('members/{user_id}', 'destroy');
    });

Route::apiResource('projects.tasks', TaskController::class)->shallow()
    ->whereNumber(['project', 'task']);

Route::apiResource('tasks.comments', CommentController::class)->shallow()
    ->except(['index', 'show'])
    ->whereNumber(['task', 'comment']);
