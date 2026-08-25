<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\AuditLogService;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService,
        protected AuditLogService $auditLogService
    ) {}

    public function profile(Request $request, ?string $type = null): Response
    {
        $user = $request->user();
        $type = $type ?: ($user->isAdminKarangTaruna() && ! $user->isSuperAdmin() ? 'karang_taruna' : 'rt');

        $org = Organization::where('type', $type)->firstOrFail();

        if (! $user->canAccessOrganization($org->id)) {
            abort(403, 'Akses tidak diizinkan untuk organisasi ini.');
        }

        return Inertia::render('admin/organization/Profile', [
            'organization' => $org,
        ]);
    }

    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $user = $request->user();
        if (! $user->canAccessOrganization($organization->id)) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'], // 2MB max
            'cover_image' => ['nullable', 'image', 'max:4096'], // 4MB max
            'social_links' => ['nullable', 'array'],
        ]);

        $oldValues = $organization->toArray();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->fileUploadService->uploadOrganizationImage(
                $request->file('logo'),
                'organizations/logos',
                $organization->logo
            );
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $this->fileUploadService->uploadOrganizationImage(
                $request->file('cover_image'),
                'organizations/covers',
                $organization->cover_image
            );
        }

        $organization->update($validated);

        // Clear cached list
        cache()->forget('global_organizations_list');

        $this->auditLogService->log('updated', 'Organization', $organization->id, $oldValues, $organization->fresh()->toArray());

        return back()->with('success', 'Profil organisasi berhasil diperbarui.');
    }
}
