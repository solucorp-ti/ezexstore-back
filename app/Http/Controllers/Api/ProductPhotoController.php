<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\StoreProductPhotoRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductPhotoController extends Controller
{
    public function store(StoreProductPhotoRequest $request, Product $product): JsonResponse
    {
        $photos = collect($request->file('photos'))->map(function ($photo) use ($product) {
            $path = $photo->store("products/{$product->id}", 'public');

            return $product->photos()->create([
                'photo_url' => Storage::url($path)
            ]);
        });

        return response()->json([
            'message' => 'Photos uploaded successfully',
            'data' => $photos
        ], 201);
    }

    public function destroy(Product $product, int $photoId): JsonResponse
    {
        $photo = $product->photos()->findOrFail($photoId);

        // Extraer el path relativo de la URL
        $path = str_replace('/storage/', '', parse_url($photo->photo_url, PHP_URL_PATH));

        // Eliminar el archivo
        Storage::disk('public')->delete($path);

        // Eliminar el registro
        $photo->delete();

        return response()->json([
            'message' => 'Photo deleted successfully'
        ]);
    }
}
