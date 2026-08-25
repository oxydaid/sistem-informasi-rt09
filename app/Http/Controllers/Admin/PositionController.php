<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationPosition;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PositionController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $orgParam = $request->query('org') ?? $request->query('org_id');
        if ($orgParam === 'rt') {
            $orgId = Organization::where('type', 'rt')->value('id');
        } elseif ($orgParam === 'karang_taruna' || $orgParam === 'kt') {
            $orgId = Organization::where('type', 'karang_taruna')->value('id');
        } elseif (is_numeric($orgParam)) {
            $orgId = (int) $orgParam;
        } else {
            $orgId = $user->organization_id ?: Organization::first()?->id;
        }

        if (! $user->canAccessOrganization($orgId)) {
            abort(403, 'Akses ditolak.');
        }

        $organizations = $user->isSuperAdmin() ? Organization::all() : Organization::where('id', $orgId)->get();
        $positions = OrganizationPosition::where('organization_id', $orgId)
            ->withCount('memberPositions')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('admin/organization/Positions', [
            'organizations' => $organizations,
            'selectedOrgId' => (int) $orgId,
            'positions' => $positions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'max_members' => ['required', 'integer', 'min:1', 'max:10'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if (! $request->user()->canAccessOrganization($validated['organization_id'])) {
            abort(403, 'Akses ditolak.');
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? (OrganizationPosition::where('organization_id', $validated['organization_id'])->max('sort_order') + 1);

        $position = OrganizationPosition::create($validated);

        $this->auditLogService->log('created', 'OrganizationPosition', $position->id, null, $position->toArray());

        return back()->with('success', 'Jabatan / Seksi berhasil ditambahkan.');
    }

    public function update(Request $request, OrganizationPosition $position): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($position->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'max_members' => ['required', 'integer', 'min:1', 'max:10'],
            'sort_order' => ['required', 'integer'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $oldValues = $position->toArray();

        $position->update($validated);

        $this->auditLogService->log('updated', 'OrganizationPosition', $position->id, $oldValues, $position->fresh()->toArray());

        return back()->with('success', 'Jabatan / Seksi berhasil diperbarui.');
    }

    public function destroy(Request $request, OrganizationPosition $position): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($position->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        if ($position->is_default) {
            return back()->with('error', 'Jabatan utama tidak dapat dihapus.');
        }

        $oldValues = $position->toArray();
        $position->delete();

        $this->auditLogService->log('deleted', 'OrganizationPosition', $position->id, $oldValues, null);

        return back()->with('success', 'Jabatan / Seksi berhasil dihapus.');
    }
}
