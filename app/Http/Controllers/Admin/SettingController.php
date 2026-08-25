<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use App\Services\FileUploadService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function __construct(
        protected SettingsService $settingsService,
        protected FileUploadService $fileUploadService,
        protected AuditLogService $auditLogService
    ) {}

    public function index(): Response
    {
        $settings = $this->settingsService->all();

        return Inertia::render('admin/settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_description' => ['nullable', 'string'],
            'contact_address' => ['nullable', 'string'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_wa' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'public_finance' => ['nullable', 'boolean'],
            'meta_keywords' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico,svg,jpg,jpeg,webp', 'max:1024'],
            'hero_banner' => ['nullable', 'image', 'max:4096'],
            'hero_image_1' => ['nullable', 'image', 'max:4096'],
            'hero_image_2' => ['nullable', 'image', 'max:4096'],
            'hero_image_3' => ['nullable', 'image', 'max:4096'],
            'hero_caption_1' => ['nullable', 'string', 'max:255'],
            'hero_caption_2' => ['nullable', 'string', 'max:255'],
            'hero_caption_3' => ['nullable', 'string', 'max:255'],
        ]);

        $oldValues = $this->settingsService->all();

        if ($request->hasFile('logo')) {
            $logoPath = $this->fileUploadService->uploadOrganizationImage(
                $request->file('logo'),
                'settings',
                $this->settingsService->get('site_logo')
            );
            $validated['site_logo'] = $logoPath;
        }

        if ($request->hasFile('favicon')) {
            $favPath = $this->fileUploadService->uploadOrganizationImage(
                $request->file('favicon'),
                'settings',
                $this->settingsService->get('site_favicon')
            );
            $validated['site_favicon'] = $favPath;
        }

        if ($request->hasFile('hero_banner')) {
            $heroPath = $this->fileUploadService->uploadOrganizationImage(
                $request->file('hero_banner'),
                'settings',
                $this->settingsService->get('hero_banner_image')
            );
            $validated['hero_banner_image'] = $heroPath;
        }

        if ($request->hasFile('hero_image_1')) {
            $path1 = $this->fileUploadService->uploadOrganizationImage(
                $request->file('hero_image_1'),
                'settings',
                $this->settingsService->get('hero_image_1')
            );
            $validated['hero_image_1'] = $path1;
        }

        if ($request->hasFile('hero_image_2')) {
            $path2 = $this->fileUploadService->uploadOrganizationImage(
                $request->file('hero_image_2'),
                'settings',
                $this->settingsService->get('hero_image_2')
            );
            $validated['hero_image_2'] = $path2;
        }

        if ($request->hasFile('hero_image_3')) {
            $path3 = $this->fileUploadService->uploadOrganizationImage(
                $request->file('hero_image_3'),
                'settings',
                $this->settingsService->get('hero_image_3')
            );
            $validated['hero_image_3'] = $path3;
        }

        unset($validated['logo'], $validated['favicon'], $validated['hero_banner']);
        unset($validated['hero_image_1'], $validated['hero_image_2'], $validated['hero_image_3']);

        $this->settingsService->setMany($validated);

        $this->auditLogService->log('updated', 'Setting', null, $oldValues, $this->settingsService->all());

        return back()->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
