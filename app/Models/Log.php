<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'object_id')->withTrashed();
    }

    public function taskStatus(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'object_id')->withTrashed();
    }

    public function taskType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class, 'object_id')->withTrashed();
    }

    public function taskByComment(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'object_id')->withTrashed();
    }

}
