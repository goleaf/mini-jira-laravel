<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public static function logAction($action, $object_id, $type)
    {
        Log::create([
            'action' => $action,
            'object_id' => $object_id,
            'type' => $type,
            'user_id' => Auth::id(),
        ]);
    }
}
