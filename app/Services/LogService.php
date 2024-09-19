<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public static function logAction(string $action, int $object_id, string $type): void
    {
        $userId = Auth::id();
        Log::create(compact('action', 'object_id', 'type', 'userId'));
    }
}
