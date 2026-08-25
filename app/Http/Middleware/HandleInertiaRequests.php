<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $settingsService = app(SettingsService::class);
        $publicSettings = $settingsService->publicSettings();

        if (empty($publicSettings['site_logo'])) {
            $publicSettings['site_logo'] = '/logo.png';
        }
        if (empty($publicSettings['site_favicon'])) {
            $publicSettings['site_favicon'] = '/favicon.svg';
        }

        return [
            ...parent::share($request),
            'appName' => $publicSettings['site_name'] ?? config('app.name', 'Sistem Informasi RT 09'),
            'settings' => $publicSettings,
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    'organization_id' => $request->user()->organization_id,
                    'phone' => $request->user()->phone,
                ] : null,
            ],
            'organizations' => cache()->remember('global_organizations_list', 3600, function () {
                return Organization::select('id', 'name', 'slug', 'type', 'logo', 'phone')->get()->toArray();
            }),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'message' => fn () => $request->session()->get('message'),
            ],
        ];
    }
}
