<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    // The attributes that are mass assignable.
    protected $fillable = ['task_id', 'user_id', 'body', 'parent_id'];

    // The attributes that should be mutated to dates.
    protected $dates = ['deleted_at'];

    /**
     * Define the relationship with the Task model.
     * A comment belongs to a task.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        // This comment is associated with a single task.
        return $this->belongsTo(Task::class);
    }

    /**
     * Define the relationship with the User model.
     * A comment belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // This comment is associated with a single user.
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship with the Comment model.
     * A comment can have many replies (which are also comments).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        // This comment can have multiple replies, which are also comments.
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Scope a query to only include comments for a specific task.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $taskId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTask($query, $taskId)
    {
        // Filter comments by the given task ID.
        return $query->where('task_id', $taskId);
    }

    /**
     * Scope a query to only include comments by a specific user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $userId)
    {
        // Filter comments by the given user ID.
        return $query->where('user_id', $userId);
    }

    /**
     * Get cached comments for a specific task.
     * This method caches the comments for 10 minutes to improve performance.
     *
     * @param int $taskId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCachedComments($taskId)
    {
        // Cache the comments for the given task ID for 10 minutes.
        return Cache::remember("comments_task_{$taskId}", now()->addMinutes(10), function () use ($taskId) {
            // Retrieve comments with their associated user and replies, filtered by task ID.
            return self::with('user', 'replies')->byTask($taskId)->get();
        });
    }
}
