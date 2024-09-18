<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function formatDate()
    {
        return $this->getDates() ;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeFilter($query, $filters)
    {
        // Implement your filtering logic here based on the $filters array
        if (isset($filters['search'])) {
            $query->where('column_name', 'like', '%' . $filters['search'] . '%');
        }

        if(request('search')){
            $query
                ->where('due','like', '%'. request('search') .'%')
                ->orWhere('created_at','like', '%'. request('search') .'%')
                ->orWhere('title','like', '%'. request('search') .'%')
                ->orWhere('description','like', '%'. request('search') .'%');
        }

        if(request('searchbody')){
            $query
                ->orWhere('title','like', '%'. request('searchbody') .'%')
                ->orWhere('description','like', '%'. request('searchbody') .'%');
        }

        return $query;

    }

    public function getAssignedUser()
    {
        $assignedUser = User::find($this->assigneduser_id);
        return ucwords($assignedUser->name);
    }

    public function getTaskCreatorUser()
    {
        $taskcreator =  User::find($this->taskcreator_id);
        return ucwords($taskcreator->name);
    }

    public function getTasksCreated()
    {
        $tasksCreated = Task::where('taskcreator_id', $this->id)->get();
        return $tasksCreated;
    }


    public function noOfTaskCreated()
    {
        return $this->getTasksCreated()->count();
    }

    public function getTasksAssigned()
    {
        $tasksAssigned = Task::where('assigneduser_id', $this->id);
        return $tasksAssigned;
    }

    public function noOfTaskAssigned()
    {
        return $this->getTasksAssigned()->count();
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

    public function noOfTaskDue()
    {
        $due = Task::where('taskcreator_id', $this->id)
            ->where('completed', 0)
            ->orWhere('assigneduser_id', $this->id)
            ->count();
        return $due;
    }

    public function noOfTaskCompleted()
    {
        return $this->noOfTaskAssigned() + $this->noOfTaskCreated() - $this->noOfTaskDue();
    }
}
