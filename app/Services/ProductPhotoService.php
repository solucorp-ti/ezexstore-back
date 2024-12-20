<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPhoto;
use App\Services\Interfaces\ProductPhotoServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductPhotoService implements ProductPhotoServiceInterface
{
    public function getPhotos(int $productId): Collection
    {
        $product = Product::findOrFail($productId);
        return $product->photos()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function uploadPhoto(Product $product, UploadedFile $file): ProductPhoto
    {
        // Generar nombre único
        $filename = $product->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Definir path relativo
        $relativePath = "products/{$product->tenant_id}/{$product->id}/{$filename}";
        
        // Guardar archivo
        $file->storeAs(
            dirname($relativePath),
            $filename,
            'public'
        );

        // Crear registro
        return $product->photos()->create([
            'photo_url' => $relativePath
        ]);
    }

    public function deletePhoto(int $productId, int $photoId): bool
    {
        $photo = ProductPhoto::where('product_id', $productId)
            ->where('id', $photoId)
            ->first();

        if (!$photo) {
            throw new NotFoundHttpException('Photo not found');
        }

        // Eliminar archivo
        if (Storage::disk('public')->exists($photo->photo_url)) {
            Storage::disk('public')->delete($photo->photo_url);
        }

        // Eliminar registro
        return $photo->delete();
    }

    /**
     * Elimina todas las fotos de un producto
     */
    public function deleteAllPhotos(int $productId): bool
    {
        $product = Product::findOrFail($productId);
        
        // Eliminar archivos
        $photos = $product->photos;
        foreach ($photos as $photo) {
            if (Storage::disk('public')->exists($photo->photo_url)) {
                Storage::disk('public')->delete($photo->photo_url);
            }
        }

        // Eliminar registros
        return $product->photos()->delete();
    }
}