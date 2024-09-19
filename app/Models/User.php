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

    public function noOfTaskCreated()
    {
        return $this->getTasksCreated()->count();
    }

    public function getTasksCreated()
    {
        return Task::where('task_creator_user_id', $this->id)->get();
    }

    public function noOfTaskAssigned()
    {
        return $this->getTasksAssigned()->count();
    }

    public function getTasksAssigned()
    {
        return Task::where('assigned_user_id', $this->id)->get();
    }

    public function totalTasks()
    {
        return $this->noOfTaskCreated() + $this->noOfTaskAssigned();
    }

    public function getAllUserTasks()
    {
        return $this->getTasksCreated()->merge($this->getTasksAssigned());
    }
}
