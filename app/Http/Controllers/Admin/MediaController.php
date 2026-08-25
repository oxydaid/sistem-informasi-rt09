<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\AuditLogService;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService,
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $type = $request->query('type'); // image, document, other

        $media = Media::with('uploader:id,name')
            ->when($search, fn ($q) => $q->where('original_name', 'like', "%{$search}%")->orWhere('caption', 'like', "%{$search}%"))
            ->when($type === 'image', fn ($q) => $q->where('mime_type', 'like', 'image/%'))
            ->when($type === 'document', fn ($q) => $q->where('mime_type', 'not like', 'image/%'))
            ->latest()
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('admin/media/Index', [
            'media' => $media,
            'filters' => [
                'search' => $search,
                'type' => $type,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'files' => ['required', 'array'],
            'files.*' => ['file', 'max:10240'], // 10MB max per file
        ]);

        foreach ($request->file('files') as $file) {
            $media = $this->fileUploadService->uploadMedia($file, $request->user()->id, 'uploads');
            $this->auditLogService->log('uploaded', 'Media', $media->id, null, $media->toArray());
        }

        return back()->with('success', 'Berkas media berhasil diunggah ke pustaka media.');
    }

    public function destroy(Request $request, Media $medium): RedirectResponse
    {
        $oldValues = $medium->toArray();

        $this->fileUploadService->deleteMedia($medium);

        $this->auditLogService->log('deleted', 'Media', $medium->id, $oldValues, null);

        return back()->with('success', 'Berkas media berhasil dihapus dari server.');
    }
}
