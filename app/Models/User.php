<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];


    public function noOfTaskCreated()
    {
        return $this->getTasksCreated()->count();
    }


    public function getTasksCreated()
    {
        $tasksCreated = Task::where('task_creator_user_id', $this->id)->get();
        return $tasksCreated;
    }

    public function noOfTaskAssigned()
    {
        return $this->getTasksAssigned()->count();
    }




    public function getTasksAssigned()
    {
        $tasksAssigned = Task::where('assigned_user_id', $this->id);
        return $tasksAssigned;
    }



    public function totalTasks()
    {
        return $this->noOfTaskCreated() + $this->noOfTaskAssigned();
    }




    public function getAllUserTasks()
    {
        $alltasks = $this->getTasksCreated()->merge($this->getTasksAssigned());
        $alltasks->all();
        return $alltasks;
    }


}
