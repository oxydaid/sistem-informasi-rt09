<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\OrganizationPeriod;
use App\Models\OrganizationPosition;
use App\Models\PositionMemberPeriod;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService,
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
        $currentOrg = Organization::with(['periods', 'positions'])->findOrFail($orgId);

        $status = $request->query('status');
        $periodId = $request->query('period_id');
        $search = $request->query('search');

        $members = OrganizationMember::where('organization_id', $orgId)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
            ->when($periodId, function ($q) use ($periodId) {
                $q->whereHas('positions', fn ($pq) => $pq->where('organization_period_id', $periodId));
            })
            ->with([
                'user:id,name,email',
                'positions.position',
                'positions.period',
            ])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $eligibleUsers = User::where(function ($q) use ($orgId) {
            $q->where('organization_id', $orgId)->orWhereNull('organization_id');
        })->where('is_active', true)->select('id', 'name', 'email')->get();

        return Inertia::render('admin/organization/Members', [
            'organizations' => $organizations,
            'currentOrg' => $currentOrg,
            'members' => $members,
            'eligibleUsers' => $eligibleUsers,
            'filters' => [
                'org_id' => (int) $orgId,
                'status' => $status,
                'period_id' => $periodId ? (int) $periodId : null,
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'bio' => ['nullable', 'string'],
            'join_year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'status' => ['required', 'in:active,inactive,alumni'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            // Optional initial position
            'period_id' => ['nullable', 'exists:organization_periods,id'],
            'position_id' => ['nullable', 'exists:organization_positions,id'],
            'position_notes' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $request->user()->canAccessOrganization($validated['organization_id'])) {
            abort(403, 'Akses ditolak.');
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $this->fileUploadService->uploadAvatar($request->file('avatar'));
        }

        $member = OrganizationMember::create($validated);

        // Assign initial position if selected
        if (! empty($validated['period_id']) && ! empty($validated['position_id'])) {
            $this->assignPosition(
                $member,
                (int) $validated['position_id'],
                (int) $validated['period_id'],
                $validated['position_notes'] ?? null
            );
        }

        $this->auditLogService->log('created', 'OrganizationMember', $member->id, null, $member->toArray());

        return back()->with('success', 'Data anggota/pengurus berhasil ditambahkan.');
    }

    public function update(Request $request, OrganizationMember $member): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($member->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'bio' => ['nullable', 'string'],
            'join_year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'status' => ['required', 'in:active,inactive,alumni'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $oldValues = $member->toArray();

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $this->fileUploadService->uploadAvatar(
                $request->file('avatar'),
                $member->avatar
            );
        }

        $member->update($validated);

        $this->auditLogService->log('updated', 'OrganizationMember', $member->id, $oldValues, $member->fresh()->toArray());

        return back()->with('success', 'Data anggota/pengurus berhasil diperbarui.');
    }

    public function destroy(Request $request, OrganizationMember $member): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($member->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $oldValues = $member->toArray();

        // Delete avatar file automatically
        if ($member->avatar) {
            $this->fileUploadService->deleteFile($member->avatar);
        }

        $member->delete();

        $this->auditLogService->log('deleted', 'OrganizationMember', $member->id, $oldValues, null);

        return back()->with('success', 'Data anggota/pengurus berhasil dihapus.');
    }

    /**
     * Assign a position in a period to a member (supports multiple periods/roles).
     */
    public function assignPositionRoute(Request $request, OrganizationMember $member): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($member->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'position_id' => ['required', 'exists:organization_positions,id'],
            'period_id' => ['required', 'exists:organization_periods,id'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $this->assignPosition(
            $member,
            (int) $validated['position_id'],
            (int) $validated['period_id'],
            $validated['notes'] ?? null
        );

        return back()->with('success', 'Jabatan kepengurusan berhasil ditautkan.');
    }

    /**
     * Remove a position assignment from a member.
     */
    public function removePositionAssignment(Request $request, PositionMemberPeriod $assignment): RedirectResponse
    {
        $member = $assignment->member;
        if (! $request->user()->canAccessOrganization($member->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $assignment->delete();

        return back()->with('success', 'Penugasan jabatan berhasil dihapus.');
    }

    protected function assignPosition(OrganizationMember $member, int $positionId, int $periodId, ?string $notes): PositionMemberPeriod
    {
        $position = OrganizationPosition::findOrFail($positionId);
        $period = OrganizationPeriod::findOrFail($periodId);

        // Check if member already has this exact position in this period
        $existing = PositionMemberPeriod::where('organization_member_id', $member->id)
            ->where('organization_position_id', $positionId)
            ->where('organization_period_id', $periodId)
            ->first();

        if ($existing) {
            $existing->update(['notes' => $notes]);

            return $existing;
        }

        // Validate max members for this position in this period (e.g. KT max 2 for secretary/treasurer)
        $currentCount = PositionMemberPeriod::where('organization_position_id', $positionId)
            ->where('organization_period_id', $periodId)
            ->count();

        if ($currentCount >= $position->max_members) {
            throw ValidationException::withMessages([
                'position_id' => "Jabatan '{$position->name}' pada periode '{$period->name}' sudah mencapai kuota maksimal ({$position->max_members} orang).",
            ]);
        }

        return PositionMemberPeriod::create([
            'organization_member_id' => $member->id,
            'organization_position_id' => $positionId,
            'organization_period_id' => $periodId,
            'notes' => $notes,
        ]);
    }
}
