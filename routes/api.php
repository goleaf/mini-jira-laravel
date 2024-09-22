<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

Route::prefix('v1')->group(function () {
    // public routes
    Route::post('/login', [ApiController::class, 'login']);

    // protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ApiController::class, 'logout']);

        // task routes
        Route::get('/tasks', [ApiController::class, 'indexTasks']);
        Route::post('/tasks', [ApiController::class, 'storeTask']);
        Route::get('/tasks/{task}', [ApiController::class, 'showTask']);
        Route::put('/tasks/{task}', [ApiController::class, 'updateTask']);
        Route::delete('/tasks/{task}', [ApiController::class, 'destroyTask']);

        // comment routes
        Route::get('/tasks/{task}/comments', [ApiController::class, 'showComments']);
        Route::post('/tasks/{task}/comments', [ApiController::class, 'storeComment']);
        Route::put('/comments/{comment}', [ApiController::class, 'updateComment']);
        Route::delete('/comments/{comment}', [ApiController::class, 'destroyComment']);

        // task status routes
        Route::get('/task-statuses', [ApiController::class, 'indexTaskStatuses']);
        Route::post('/task-statuses', [ApiController::class, 'storeTaskStatus']);
        Route::get('/task-statuses/{taskStatus}', [ApiController::class, 'showTaskStatus']);
        Route::put('/task-statuses/{taskStatus}', [ApiController::class, 'updateTaskStatus']);
        Route::delete('/task-statuses/{taskStatus}', [ApiController::class, 'destroyTaskStatus']);

        // task type routes
        Route::get('/task-types', [ApiController::class, 'indexTaskTypes']);
        Route::post('/task-types', [ApiController::class, 'storeTaskType']);
        Route::get('/task-types/{taskType}', [ApiController::class, 'showTaskType']);
        Route::put('/task-types/{taskType}', [ApiController::class, 'updateTaskType']);
        Route::delete('/task-types/{taskType}', [ApiController::class, 'destroyTaskType']);
    });
});
