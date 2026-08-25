<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload an avatar and delete the old one if exists.
     */
    public function uploadAvatar(UploadedFile $file, ?string $oldPath = null, string $disk = 'public'): string
    {
        $this->deleteFile($oldPath, $disk);

        $filename = (string) Str::ulid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, $disk);

        return $path;
    }

    /**
     * Upload a thumbnail image and delete the old one if exists.
     */
    public function uploadThumbnail(UploadedFile $file, ?string $oldPath = null, string $disk = 'public'): string
    {
        $this->deleteFile($oldPath, $disk);

        $filename = (string) Str::ulid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('thumbnails', $filename, $disk);

        return $path;
    }

    /**
     * Upload an organization logo or cover and delete the old one if exists.
     */
    public function uploadOrganizationImage(UploadedFile $file, string $folder = 'organizations', ?string $oldPath = null, string $disk = 'public'): string
    {
        $this->deleteFile($oldPath, $disk);

        $filename = (string) Str::ulid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, $disk);

        return $path;
    }

    /**
     * Upload a finance transaction receipt attachment and delete the old one if exists.
     */
    public function uploadReceipt(UploadedFile $file, ?string $oldPath = null, string $disk = 'public'): string
    {
        $this->deleteFile($oldPath, $disk);

        $filename = (string) Str::ulid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('receipts/'.date('Y/m'), $filename, $disk);

        return $path;
    }

    /**
     * Upload a media file into Media library with metadata record.
     */
    public function uploadMedia(UploadedFile $file, ?int $userId = null, string $folder = 'media', string $disk = 'public'): Media
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $fileSize = $file->getSize();

        $filename = (string) Str::ulid().'.'.$extension;
        $path = $file->storeAs($folder.'/'.date('Y/m'), $filename, $disk);

        $width = null;
        $height = null;
        if (str_starts_with($mimeType, 'image/')) {
            $imageDimensions = @getimagesize($file->getRealPath());
            if ($imageDimensions) {
                $width = $imageDimensions[0];
                $height = $imageDimensions[1];
            }
        }

        return Media::create([
            'disk' => $disk,
            'file_path' => $path,
            'original_name' => $originalName,
            'file_name' => $filename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'width' => $width,
            'height' => $height,
            'uploaded_by_user_id' => $userId,
        ]);
    }

    /**
     * Safely delete a file from disk if it exists.
     */
    public function deleteFile(?string $path, string $disk = 'public'): bool
    {
        if (! $path) {
            return false;
        }

        // Clean any leading '/storage/' or 'storage/' from path if stored as web URL
        $cleanPath = preg_replace('#^/?storage/#', '', $path);

        if (Storage::disk($disk)->exists($cleanPath)) {
            return Storage::disk($disk)->delete($cleanPath);
        }

        return false;
    }

    /**
     * Delete a media model and its underlying file.
     */
    public function deleteMedia(Media $media): bool
    {
        $this->deleteFile($media->file_path, $media->disk);

        return $media->delete();
    }
}
