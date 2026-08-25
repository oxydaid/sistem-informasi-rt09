<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $role = $request->query('role');

        $users = User::with('organization')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
            ->when($role, fn ($q) => $q->where('role', $role))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $organizations = Organization::select('id', 'name', 'type')->get();

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'organizations' => $organizations,
            'filters' => [
                'search' => $search,
                'role' => $role,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'in:super_admin,admin_rt,admin_karang_taruna'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $this->auditLogService->log('created', 'User', $user->id, null, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);

        return back()->with('success', 'Akun admin berhasil dibuat.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', Password::defaults()],
            'role' => ['required', 'in:super_admin,admin_rt,admin_karang_taruna'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
        ];

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        $this->auditLogService->log('updated', 'User', $user->id, $oldValues, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
        ]);

        return back()->with('success', 'Akun admin berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $user->delete();

        $this->auditLogService->log('deleted', 'User', $user->id, $oldValues, null);

        return back()->with('success', 'Akun admin berhasil dihapus.');
    }
}
