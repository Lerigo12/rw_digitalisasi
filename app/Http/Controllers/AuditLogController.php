<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')->latest()->paginate(15);

        return view('audit_logs.index', compact('logs'));
    }
}
