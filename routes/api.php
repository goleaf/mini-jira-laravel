<?php

    use App\Http\Controllers\Api\CommentApiController;
    use App\Http\Controllers\Api\TaskStatusApiController;
    use App\Http\Controllers\Api\TaskTypeApiController;
    use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskApiController;

// API version 1
Route::prefix('v1')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskApiController::class, 'index'])->name('tasks.index');
        Route::get('/{task}', [TaskApiController::class, 'show'])->name('tasks.show');
        Route::post('/', [TaskApiController::class, 'store'])->name('tasks.store');
        Route::put('/{task}', [TaskApiController::class, 'update'])->name('tasks.update');
        Route::delete('/{task}', [TaskApiController::class, 'destroy'])->name('tasks.destroy');
        // comments
        Route::get('/{task}/comments', [CommentApiController::class, 'show'])->name('comments.show');
        Route::post('/{task}/comments', [CommentApiController::class, 'store'])->name('comments.store');
    });

    Route::prefix('task-statuses')->group(function () {
        Route::get('/', [TaskStatusApiController::class, 'index']);
        Route::post('/', [TaskStatusApiController::class, 'store']);
        Route::put('/{taskStatus}', [TaskStatusApiController::class, 'update']);
        Route::delete('/{taskStatus}', [TaskStatusApiController::class, 'destroy']);
    });

    Route::prefix('task-types')->group(function () {
        Route::get('/', [TaskTypeApiController::class, 'index']);
        Route::post('/', [TaskTypeApiController::class, 'store']);
        Route::put('/{taskType}', [TaskTypeApiController::class, 'update']);
        Route::delete('/{taskType}', [TaskTypeApiController::class, 'destroy']);
    });

});
