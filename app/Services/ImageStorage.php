<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageStorage
{
    public const GALLERY_MAX = 1920;
    public const THUMBNAIL_MAX = 1200;

    private const WEBP_QUALITY = 82;

    /** Uloží obrázek na disk "public" zmenšený a převedený do WebP; vrací relativní cestu. */
    public function store(UploadedFile $file, string $directory, int $maxSize): string
    {
        // Hosting bez GD nebo bez podpory WebP: uložíme originál, ať nahrávání nespadne.
        if (! function_exists('imagewebp') || ! (imagetypes() & IMG_WEBP)) {
            return $file->store($directory, 'public');
        }

        $image = $this->load($file);

        // GIF necháváme v originále kvůli animaci; nečitelné formáty také.
        if ($image === null) {
            return $file->store($directory, 'public');
        }

        $image = $this->resize($image, $maxSize);

        ob_start();
        imagewebp($image, null, self::WEBP_QUALITY);
        $data = ob_get_clean();
        imagedestroy($image);

        $path = $directory.'/'.Str::uuid().'.webp';
        Storage::disk('public')->put($path, $data);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function load(UploadedFile $file): ?GdImage
    {
        $realPath = $file->getRealPath();
        $type = @getimagesize($realPath)[2] ?? null;

        $image = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($realPath),
            IMAGETYPE_PNG => @imagecreatefrompng($realPath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($realPath),
            default => false,
        };

        if (! $image) {
            return null;
        }

        if ($type === IMAGETYPE_JPEG) {
            $image = $this->applyExifOrientation($image, $realPath);
        }

        return $image;
    }

    /** Fotky z mobilů bývají uložené "na boku" a otočení je jen v EXIF. */
    private function applyExifOrientation(GdImage $image, string $path): GdImage
    {
        $orientation = function_exists('exif_read_data') ? (@exif_read_data($path)['Orientation'] ?? 1) : 1;

        $angle = match ((int) $orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);
        imagedestroy($image);

        return $rotated;
    }

    private function resize(GdImage $image, int $maxSize): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, $maxSize / max($width, $height));

        $newWidth = (int) round($width * $scale);
        $newHeight = (int) round($height * $scale);

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        return $canvas;
    }
}
