<?php

    use App\Http\Controllers\Auth\LoginRegisterController;
    use App\Http\Controllers\CommentController;
    use App\Http\Controllers\LogsController;
    use App\Http\Controllers\TaskController;
    use App\Http\Controllers\TaskStatusController;
    use App\Http\Controllers\TaskTypeController;
    use App\Http\Controllers\UserController;
    use Illuminate\Support\Facades\Route;


    Route::get('/', [TaskController::class, 'index'])->name('home');

Route::controller(LoginRegisterController::class)->group(function() {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware(['auth'])->group(function () {

    // tasks
    Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [taskController::class, 'create'])->name('tasks.create');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::post('/tasks/', [TaskController::class, 'update'])->name('tasks.update');
    Route::post('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::post('/tasks/update-pagination-count', [TaskController::class, 'updatePaginationCount'])->name('tasks.update-pagination-count');

    // comments
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');

    Route::get('user/{user}/', [UserController::class, 'userDashboard'])->name('user.dashboard');

    // task types
    Route::get('/task-types', [TaskTypeController::class, 'index'])->name('task-types.index');
    Route::get('/task-types/create', [TaskTypeController::class, 'create'])->name('task-types.create');
    Route::post('/task-types', [TaskTypeController::class, 'store'])->name('task-types.store');
    Route::get('/task-types/{task_type}/edit', [TaskTypeController::class, 'edit'])->name('task-types.edit');
    Route::put('/task-types/{task_type}', [TaskTypeController::class, 'update'])->name('task-types.update');
    Route::delete('/task-types/{task_type}', [TaskTypeController::class, 'destroy'])->name('task-types.destroy');

    // task statuses
    Route::get('/task-statuses', [TaskStatusController::class, 'index'])->name('task-statuses.index');
    Route::get('/task-statuses/create', [TaskStatusController::class, 'create'])->name('task-statuses.create');
    Route::post('/task-statuses', [TaskStatusController::class, 'store'])->name('task-statuses.store');
    Route::get('/task-statuses/{taskStatus}/edit', [TaskStatusController::class, 'edit'])->name('task-statuses.edit');
    Route::put('/task-statuses/{taskStatus}', [TaskStatusController::class, 'update'])->name('task-statuses.update');
    Route::delete('/task-statuses/{taskStatus}', [TaskStatusController::class, 'destroy'])->name('task-statuses.destroy');

    // logs
    Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');

    Route::resources(['task' => TaskController::class ]);

});


