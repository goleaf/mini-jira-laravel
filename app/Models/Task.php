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

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'task_type_id');
    }

    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    public function getTaskCreatorUserAttribute()
    {
        return $this->getUserName($this->task_creator_user_id);
    }

    public function getAssignedUserAttribute()
    {
        return $this->getUserName($this->assigned_user_id);
    }

    public function getAssignedTesterAttribute()
    {
        return $this->getUserName($this->assigned_tester_user_id);
    }

    private function getUserName($userId)
    {
        return ucwords(User::find($userId)->name ?? 'Unknown');
    }
}
