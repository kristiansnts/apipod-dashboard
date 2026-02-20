<?php

namespace App\Http\Controllers;

use App\Models\LlmModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function models()
    {
        $models = LlmModel::with('provider')->get();
        return view('dashboard.models', compact('models'));
    }

    public function usage()
    {
        $user = auth()->user();
        $usageLogs = $user->usageLogs()->orderBy('usage_id', 'desc')->paginate(15);
        return view('dashboard.usage', compact('usageLogs', 'user'));
    }

    public function apiKeys()
    {
        $user = auth()->user();
        return view('dashboard.api-keys', compact('user'));
    }

    public function analytics()
    {
        $user = auth()->user();
        $logs = $user->usageLogs()->orderBy('usage_id', 'desc')->get();
        return view('dashboard.analytics', compact('logs'));
    }
}
