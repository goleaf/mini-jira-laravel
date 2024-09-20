<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'task_deadline_date',
        'task_creator_user_id',
        'assigned_user_id',
        'assigned_tester_user_id',
        'task_type_id',
        'task_status_id',
    ];

    protected $withCount = ['comments'];

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function taskCreator()
    {
        return $this->belongsTo(User::class, 'task_creator_user_id')->withCount('tasksCreated as tasks_created_count');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id')->withCount('tasksAssigned as tasks_assigned_count');
    }

    public function assignedTester()
    {
        return $this->belongsTo(User::class, 'assigned_tester_user_id')->withCount('tasksAssigned as tasks_assigned_count');
    }

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'task_type_id')->withCount('tasks');
    }

    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id')->withCount('tasks');
    }

    public function getTaskCreatorTasksCountAttribute()
    {
        return $this->taskCreator->tasks_created_count;
    }

    public function getAssignedUserTasksCountAttribute()
    {
        return $this->assignedUser->tasks_assigned_count;
    }

    public function getAssignedTesterTasksCountAttribute()
    {
        return $this->assignedTester->tasks_assigned_count;
    }

    public function getTaskTypeCountAttribute()
    {
        return $this->taskType->tasks_count;
    }

    public function getTaskStatusCountAttribute()
    {
        return $this->taskStatus->tasks_count;
    }
}
