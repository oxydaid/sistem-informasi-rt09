<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('auth/Login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $user = Auth::user();
        if (! $user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
            ]);
        }

        $request->session()->regenerate();

        app(AuditLogService::class)->log('login', 'User', $user->id, null, ['name' => $user->name, 'email' => $user->email], $user->id);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        app(AuditLogService::class)->log('logout', 'User', $userId, null, null, $userId);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
