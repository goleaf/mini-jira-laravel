<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Log::with('user')->latest();

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('loggable_type') && $request->loggable_type != '') {
            $query->where('loggable_type', $request->loggable_type);
        }

        if ($request->has('loggable_id') && $request->loggable_id != '') {
            $query->where('loggable_id', $request->loggable_id);
        }

        // Add date range filter
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);
        $users = User::orderBy('name')->get();
        $modelTypes = Log::distinct('loggable_type')->pluck('loggable_type')->toArray();

        return view('log.index', compact('logs', 'users', 'modelTypes'));
    }

    public static function log($action, $loggable_id, $loggable_type)
    {
        Log::create([
            'user_id' => auth()->id() ?? 0, 
            'action' => $action,
            'loggable_id' => $loggable_id,
            'loggable_type' => $loggable_type,
        ]);
    }
}