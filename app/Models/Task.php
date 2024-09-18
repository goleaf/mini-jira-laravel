<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('task_creator_user_id', $userId);
    }

    public function taskCreator()
    {
        return $this->belongsTo(User::class, 'task_creator_user_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function getTaskTypeId()
    {
        return $this->belongsTo(TaskType::class, 'task_type_id');
    }

    public function getTaskStatusId()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    public function getTaskCreatorUser()
    {
        $taskcreator =  User::findOrFail($this->task_creator_user_id);
        return ucwords($taskcreator->name);
    }

    public function getAssignedUser()
    {
        $assignedUser = User::findOrFail($this->assigned_user_id);
        return ucwords($assignedUser->name);
    }

    public function getAssignedTester()
    {
        $assignedTester = User::findOrFail($this->assigned_tester_user_id);
        return ucwords($assignedTester->name);
    }

}
