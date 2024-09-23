<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class LogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $query = Log::with('user')->latest();

        $query->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
              ->when($request->filled('loggable_type'), fn ($q) => $q->where('loggable_type', $request->loggable_type))
              ->when($request->filled('loggable_id'), fn ($q) => $q->where('loggable_id', $request->loggable_id))
              ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
              ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to));

        $logs = $query->paginate(20);
        $users = User::orderBy('name')->get();
        $modelTypes = Log::distinct()->pluck('loggable_type')->toArray();

        return view('log.index', compact('logs', 'users', 'modelTypes'));
    }

    public static function log(string $action, int|string $loggable_id, string $loggable_type): void
    {
        $logData = [
            'user_id' => auth()->id(),
            'action' => $action,
            'loggable_id' => $loggable_id,
            'loggable_type' => $loggable_type,
        ];

        Log::create($logData);
    }
}