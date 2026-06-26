<?php

namespace App\Http\Controllers;

use App\Models\UserLoginLog;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    public function index()
    {
        $logs = UserLoginLog::query()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view(
            'users.activity',
            compact('logs')
        );
    }
}