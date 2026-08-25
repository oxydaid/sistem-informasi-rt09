<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationPeriod;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PeriodController extends Controller
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
        $periods = OrganizationPeriod::where('organization_id', $orgId)
            ->withCount('memberPositions')
            ->orderByDesc('start_year')
            ->get();

        return Inertia::render('admin/organization/Periods', [
            'organizations' => $organizations,
            'selectedOrgId' => (int) $orgId,
            'periods' => $periods,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'start_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'end_year' => ['required', 'integer', 'gte:start_year', 'max:2100'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        if (! $request->user()->canAccessOrganization($validated['organization_id'])) {
            abort(403, 'Akses ditolak.');
        }

        if (! empty($validated['is_active'])) {
            OrganizationPeriod::where('organization_id', $validated['organization_id'])
                ->update(['is_active' => false]);
        }

        $period = OrganizationPeriod::create($validated);

        $this->auditLogService->log('created', 'OrganizationPeriod', $period->id, null, $period->toArray());

        return back()->with('success', 'Periode kepengurusan berhasil ditambahkan.');
    }

    public function update(Request $request, OrganizationPeriod $period): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($period->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'end_year' => ['required', 'integer', 'gte:start_year', 'max:2100'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldValues = $period->toArray();

        if (! empty($validated['is_active']) && ! $period->is_active) {
            OrganizationPeriod::where('organization_id', $period->organization_id)
                ->where('id', '!=', $period->id)
                ->update(['is_active' => false]);
        }

        $period->update($validated);

        $this->auditLogService->log('updated', 'OrganizationPeriod', $period->id, $oldValues, $period->fresh()->toArray());

        return back()->with('success', 'Periode kepengurusan berhasil diperbarui.');
    }

    public function destroy(Request $request, OrganizationPeriod $period): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($period->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $oldValues = $period->toArray();
        $period->delete();

        $this->auditLogService->log('deleted', 'OrganizationPeriod', $period->id, $oldValues, null);

        return back()->with('success', 'Periode kepengurusan berhasil dihapus.');
    }
}
