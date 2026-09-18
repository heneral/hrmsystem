<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('users.manage'), 403);
        $logs = AuditLog::with('user')->when($request->action, fn ($query, $action) => $query->where('action', $action))->latest('created_at')->paginate(50)->withQueryString();
        $summary = ['total' => AuditLog::count(), 'today' => AuditLog::whereDate('created_at', today())->count(), 'actors' => AuditLog::whereNotNull('user_id')->distinct('user_id')->count('user_id')];
        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');

        return view('admin/audit-logs', compact('logs', 'summary', 'actions'));
    }
}