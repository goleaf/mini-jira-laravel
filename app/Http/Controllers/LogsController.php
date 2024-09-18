<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index()
    {
        $logs = Log::latest()->paginate(20);

        return view('log.index', compact('logs'));
    }
}
