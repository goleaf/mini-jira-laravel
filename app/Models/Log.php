<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'object_id')->withTrashed();
    }

    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'object_id')->withTrashed();
    }

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'object_id')->withTrashed();
    }

    public function taskByComment()
    {
        return $this->belongsTo(Task::class, 'object_id')->withTrashed();
    }


}
