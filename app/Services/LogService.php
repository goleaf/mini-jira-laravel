<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public static function logAction(string $action, ?int $objectId, string $type): void
    {
        Log::create([
            'action' => $action,
            'object_id' => $objectId ?? 0, // Use 0 as a default value if $objectId is null
            'type' => $type,
            'user_id' => Auth::id(),
        ]);
    }
}
