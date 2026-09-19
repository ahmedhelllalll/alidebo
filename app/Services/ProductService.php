<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductService
{
    public function handleImageUpload(?UploadedFile $file, string $directory): ?string
    {
        if (!$file) return null;
        return $file->store($directory, 'r2');
    }

    public function deleteImage(?string $path, string $disk = 'r2'): void
    {
        if ($path) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function uploadMedia(Product $product, array $files, bool $firstIsPrimary = false): array
    {
        $uploaded = [];
        $existingCount = $product->images()->count();

        foreach ($files as $index => $image) {
            $path = $this->handleImageUpload($image, 'product_images');
            
            $isPrimary = false;
            if ($existingCount === 0 && $index === 0 && $firstIsPrimary) {
                $isPrimary = true;
            }

            $media = $product->images()->create([
                'file_path' => $path,
                'is_primary' => $isPrimary,
                'disk' => 'r2',
            ]);
            
            $uploaded[] = $media;
        }

        return $uploaded;
    }

    public function deleteMedia(ProductImage $media): void
    {
        $this->deleteImage($media->file_path, $media->disk ?? 'r2');
        $media->delete();
    }

    public function syncSpecifications(Product $product, ?array $specifications): void
    {
        // First, clear existing specs to prevent duplicates or stale data
        $product->specifications()->delete();

        if (empty($specifications)) {
            return;
        }

        foreach ($specifications as $spec) {
            if (!empty($spec['name']) && is_array($spec['name'])) {
                $product->specifications()->create([
                    'name' => $spec['name'],
                    'value' => $spec['value'] ?? null,
                ]);
            }
        }
    }
}
