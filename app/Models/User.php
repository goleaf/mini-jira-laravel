<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function tasksCreated()
    {
        return $this->hasMany(Task::class, 'task_creator_user_id');
    }

    public function tasksAssigned()
    {
        return $this->hasMany(Task::class, 'assigned_user_id');
    }

    public function tasks()
    {
        return $this->tasksCreated()->union($this->tasksAssigned());
    }

    public function noOfTaskCreated()
    {
        return $this->tasksCreated()->count();
    }

    public function getTasksCreated()
    {
        return $this->tasksCreated;
    }

    public function noOfTaskAssigned()
    {
        return $this->tasksAssigned()->count();
    }

    public function getTasksAssigned()
    {
        return $this->tasksAssigned;
    }

    public function totalTasks()
    {
        return $this->noOfTaskCreated() + $this->noOfTaskAssigned();
    }

    public function getAllUserTasks()
    {
        return $this->tasksCreated->merge($this->tasksAssigned);
    }
}
