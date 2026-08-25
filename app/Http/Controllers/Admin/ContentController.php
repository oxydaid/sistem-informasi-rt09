<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityMedia;
use App\Models\Content;
use App\Models\ContentAttachment;
use App\Models\Event;
use App\Models\Media;
use App\Models\Organization;
use App\Services\AuditLogService;
use App\Services\FileUploadService;
use App\Services\TiptapContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ContentController extends Controller
{
    public function __construct(
        protected TiptapContentService $tiptapService,
        protected FileUploadService $fileUploadService,
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $orgId = $request->query('org_id') ?: ($user->organization_id ?: Organization::first()?->id);

        if (! $user->canAccessOrganization($orgId)) {
            abort(403, 'Akses ditolak.');
        }

        $organizations = $user->isSuperAdmin() ? Organization::all() : Organization::where('id', $orgId)->get();

        $type = $request->query('type'); // announcement, article, event, activity, page
        $status = $request->query('status');
        $search = $request->query('search');

        $contents = Content::where('organization_id', $orgId)
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->with(['author:id,name', 'event', 'activity'])
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/contents/Index', [
            'organizations' => $organizations,
            'selectedOrgId' => (int) $orgId,
            'contents' => $contents,
            'filters' => [
                'type' => $type,
                'status' => $status,
                'search' => $search,
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $user = $request->user();
        $orgId = $request->query('org_id') ?: ($user->organization_id ?: Organization::first()?->id);
        $type = $request->query('type', 'announcement');

        if (! $user->canAccessOrganization($orgId)) {
            abort(403, 'Akses ditolak.');
        }

        $organizations = $user->isSuperAdmin() ? Organization::all() : Organization::where('id', $orgId)->get();

        return Inertia::render('admin/contents/Form', [
            'organizations' => $organizations,
            'selectedOrgId' => (int) $orgId,
            'type' => $type,
            'contentItem' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'type' => ['required', 'in:announcement,article,event,activity,page'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:contents,slug'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable'], // JSON or array
            'thumbnail' => ['nullable', 'image', 'max:3072'],
            'status' => ['required', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
            // Event fields
            'event_start_at' => ['nullable', 'required_if:type,event', 'date'],
            'event_end_at' => ['nullable', 'date', 'after_or_equal:event_start_at'],
            'event_location' => ['nullable', 'required_if:type,event', 'string', 'max:255'],
            'event_maps_url' => ['nullable', 'string'],
            'event_is_online' => ['boolean'],
            // Activity fields
            'activity_date' => ['nullable', 'required_if:type,activity', 'date'],
            'activity_location' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $request->user()->canAccessOrganization($validated['organization_id'])) {
            abort(403, 'Akses ditolak.');
        }

        $slug = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Content::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = "{$slug}-{$counter}";
            $counter++;
        }
        $validated['slug'] = $uniqueSlug;

        $validated['author_id'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $this->fileUploadService->uploadThumbnail($request->file('thumbnail'));
        }

        // Tiptap Content parsing
        $contentData = $validated['content'] ?? null;
        if (is_string($contentData)) {
            $contentData = json_decode($contentData, true) ?? $contentData;
        }
        $validated['content'] = is_array($contentData) ? $contentData : null;
        $validated['content_html'] = $this->tiptapService->renderToHtml($validated['content']);

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $content = Content::create($validated);

        // Create specific relations
        if ($validated['type'] === 'event') {
            Event::create([
                'content_id' => $content->id,
                'start_at' => $validated['event_start_at'],
                'end_at' => $validated['event_end_at'] ?? null,
                'location' => $validated['event_location'],
                'maps_url' => $validated['event_maps_url'] ?? null,
                'is_online' => $validated['event_is_online'] ?? false,
            ]);
        } elseif ($validated['type'] === 'activity') {
            Activity::create([
                'content_id' => $content->id,
                'activity_date' => $validated['activity_date'],
                'location' => $validated['activity_location'] ?? null,
            ]);
        }

        $this->auditLogService->log('created', 'Content', $content->id, null, $content->toArray());

        return redirect()->route('admin.contents.index', ['org_id' => $content->organization_id, 'type' => $content->type])
            ->with('success', 'Konten berhasil disimpan.');
    }

    public function edit(Request $request, Content $content): Response
    {
        if (! $request->user()->canAccessOrganization($content->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $content->load(['event', 'activity.gallery.media', 'attachments.media']);

        $organizations = $request->user()->isSuperAdmin() ? Organization::all() : Organization::where('id', $content->organization_id)->get();

        return Inertia::render('admin/contents/Form', [
            'organizations' => $organizations,
            'selectedOrgId' => (int) $content->organization_id,
            'type' => $content->type,
            'contentItem' => $content,
        ]);
    }

    public function update(Request $request, Content $content): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($content->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:contents,slug,'.$content->id],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable'],
            'thumbnail' => ['nullable', 'image', 'max:3072'],
            'status' => ['required', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
            // Event fields
            'event_start_at' => ['nullable', 'required_if:type,event', 'date'],
            'event_end_at' => ['nullable', 'date', 'after_or_equal:event_start_at'],
            'event_location' => ['nullable', 'required_if:type,event', 'string', 'max:255'],
            'event_maps_url' => ['nullable', 'string'],
            'event_is_online' => ['boolean'],
            // Activity fields
            'activity_date' => ['nullable', 'required_if:type,activity', 'date'],
            'activity_location' => ['nullable', 'string', 'max:255'],
        ]);

        $oldValues = $content->toArray();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $this->fileUploadService->uploadThumbnail(
                $request->file('thumbnail'),
                $content->thumbnail
            );
        }

        // Tiptap Content update & media cleanup
        $contentData = $validated['content'] ?? null;
        if (is_string($contentData)) {
            $contentData = json_decode($contentData, true) ?? $contentData;
        }
        $validated['content'] = is_array($contentData) ? $contentData : null;
        $validated['content_html'] = $this->tiptapService->renderToHtml($validated['content']);

        // Clean up orphan media that were removed from the editor
        $this->tiptapService->cleanupOrphanedMedia($content->content, $validated['content']);

        if ($validated['status'] === 'published' && empty($content->published_at) && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $content->update($validated);

        if ($content->type === 'event') {
            Event::updateOrCreate(
                ['content_id' => $content->id],
                [
                    'start_at' => $validated['event_start_at'],
                    'end_at' => $validated['event_end_at'] ?? null,
                    'location' => $validated['event_location'],
                    'maps_url' => $validated['event_maps_url'] ?? null,
                    'is_online' => $validated['event_is_online'] ?? false,
                ]
            );
        } elseif ($content->type === 'activity') {
            Activity::updateOrCreate(
                ['content_id' => $content->id],
                [
                    'activity_date' => $validated['activity_date'],
                    'location' => $validated['activity_location'] ?? null,
                ]
            );
        }

        $this->auditLogService->log('updated', 'Content', $content->id, $oldValues, $content->fresh()->toArray());

        return redirect()->route('admin.contents.index', ['org_id' => $content->organization_id, 'type' => $content->type])
            ->with('success', 'Konten berhasil diperbarui.');
    }

    public function destroy(Request $request, Content $content): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($content->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $oldValues = $content->toArray();

        // 1. Delete thumbnail file
        if ($content->thumbnail) {
            $this->fileUploadService->deleteFile($content->thumbnail);
        }

        // 2. Cleanup all tiptap embedded media
        $this->tiptapService->cleanupOrphanedMedia($content->content, null);

        // 3. Cleanup attachments
        foreach ($content->attachments as $attachment) {
            if ($attachment->media) {
                $this->fileUploadService->deleteMedia($attachment->media);
            }
        }

        // 4. If activity, cleanup gallery media
        if ($content->activity) {
            foreach ($content->activity->gallery as $actMedia) {
                if ($actMedia->media) {
                    $this->fileUploadService->deleteMedia($actMedia->media);
                }
            }
        }

        $content->delete();

        $this->auditLogService->log('deleted', 'Content', $content->id, $oldValues, null);

        return back()->with('success', 'Konten dan seluruh lampiran berkasnya berhasil dihapus.');
    }

    /**
     * Upload image/media endpoint used directly by Tiptap editor.
     */
    public function uploadEditorMedia(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf,docx,xlsx', 'max:5120'], // 5MB max
        ]);

        $media = $this->fileUploadService->uploadMedia(
            $request->file('file'),
            $request->user()->id,
            'editor'
        );

        return response()->json([
            'success' => true,
            'mediaId' => $media->id,
            'url' => $media->url,
            'name' => $media->original_name,
            'size' => number_format($media->file_size / 1024, 1).' KB',
        ]);
    }

    /**
     * Upload activity gallery images.
     */
    public function uploadGallery(Request $request, Content $content): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($content->organization_id) || $content->type !== 'activity') {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'photos' => ['required', 'array'],
            'photos.*' => ['image', 'max:4096'],
        ]);

        $activity = $content->activity()->firstOrCreate([
            'activity_date' => now()->toDateString(),
        ]);

        $lastSort = (int) ActivityMedia::where('activity_id', $activity->id)->max('sort_order');

        foreach ($request->file('photos') as $photo) {
            $media = $this->fileUploadService->uploadMedia($photo, $request->user()->id, 'activities/gallery');
            $lastSort++;
            ActivityMedia::create([
                'activity_id' => $activity->id,
                'media_id' => $media->id,
                'sort_order' => $lastSort,
            ]);
        }

        return back()->with('success', 'Foto galeri berhasil diunggah.');
    }

    /**
     * Delete gallery photo.
     */
    public function deleteGalleryPhoto(Request $request, ActivityMedia $activityMedia): RedirectResponse
    {
        $content = $activityMedia->activity->content;
        if (! $request->user()->canAccessOrganization($content->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        if ($activityMedia->media) {
            $this->fileUploadService->deleteMedia($activityMedia->media);
        }

        $activityMedia->delete();

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }

    /**
     * Upload content attachment document.
     */
    public function uploadAttachment(Request $request, Content $content): RedirectResponse
    {
        if (! $request->user()->canAccessOrganization($content->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip', 'max:10240'],
            'display_name' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('document');
        $media = $this->fileUploadService->uploadMedia($file, $request->user()->id, 'attachments');

        $lastSort = (int) ContentAttachment::where('content_id', $content->id)->max('sort_order');

        ContentAttachment::create([
            'content_id' => $content->id,
            'media_id' => $media->id,
            'display_name' => $request->input('display_name') ?: $file->getClientOriginalName(),
            'sort_order' => $lastSort + 1,
        ]);

        return back()->with('success', 'Dokumen lampiran berhasil diunggah.');
    }

    /**
     * Delete content attachment document.
     */
    public function deleteAttachment(Request $request, ContentAttachment $attachment): RedirectResponse
    {
        $content = $attachment->content;
        if (! $request->user()->canAccessOrganization($content->organization_id)) {
            abort(403, 'Akses ditolak.');
        }

        if ($attachment->media) {
            $this->fileUploadService->deleteMedia($attachment->media);
        }

        $attachment->delete();

        return back()->with('success', 'Dokumen lampiran berhasil dihapus.');
    }

    /**
     * Import markdown file and convert to Tiptap JSON.
     */
    public function importMarkdown(Request $request): JsonResponse
    {
        $request->validate([
            'markdown_file' => ['required', 'file', 'mimes:txt,md,markdown', 'max:2048'],
        ]);

        $content = file_get_contents($request->file('markdown_file')->getRealPath());
        $tiptapJson = $this->tiptapService->markdownToTiptapJson($content);

        return response()->json([
            'success' => true,
            'tiptap' => $tiptapJson,
            'html' => $this->tiptapService->renderToHtml($tiptapJson),
        ]);
    }
}
