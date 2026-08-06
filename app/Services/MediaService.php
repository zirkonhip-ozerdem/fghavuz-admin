<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Public (kimliksiz) upload akislari icin merkezi dosya yazma/silme servisi
 * (Madde 16 - "Upload islemleri merkezi MediaService uzerinden yapilmali").
 *
 * Not: Admin panelindeki (Filament) yuklemeler Filament'in kendi FileUpload /
 * SpatieMediaLibraryFileUpload bilesenleri uzerinden, Product galeri/dokuman
 * yuklemeleri ise Spatie Media Library uzerinden yonetilir - bu servis
 * ozellikle QuoteRequest gibi public form eklerini kapsar.
 */
class MediaService
{
    public function __construct(private readonly string $disk = 'public')
    {
    }

    public function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, $this->disk);
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    public function url(?string $path): ?string
    {
        return $path ? Storage::disk($this->disk)->url($path) : null;
    }
}
