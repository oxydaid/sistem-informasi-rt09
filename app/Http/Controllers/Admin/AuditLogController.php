<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $action = $request->query('action');

        $logs = AuditLog::with('user:id,name,email,role')
            ->when($search, function ($q) use ($search) {
                $q->where('model_type', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            })
            ->when($action, fn ($q) => $q->where('action', $action))
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/audit_logs/Index', [
            'logs' => $logs,
            'filters' => [
                'search' => $search,
                'action' => $action,
            ],
        ]);
    }
}
