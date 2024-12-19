<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\StoreProductPhotoRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductPhotoController extends BaseApiController
{
    public function index(Request $request, Product $product): JsonResponse
    {
        $photos = $product->photos()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $photos->items(),
            'meta' => [
                'current_page' => $photos->currentPage(),
                'last_page' => $photos->lastPage(),
                'per_page' => $photos->perPage(),
                'total' => $photos->total()
            ],
            'message' => 'Product photos retrieved successfully'
        ]);
    }

    public function store(StoreProductPhotoRequest $request, Product $product): JsonResponse  
    {
        $photos = collect($request->file('photos'))->map(function ($photo) use ($product) {
            $path = $photo->store("products/{$product->id}", 'public');
            
            return $product->photos()->create([
                'photo_url' => Storage::url($path)
            ]);
        });

        return response()->json([
            'success' => true,
            'data' => $photos,
            'message' => 'Photos uploaded successfully'
        ], 201);
    }

    public function destroy(Product $product, int $photoId): JsonResponse
    {
        $photo = $product->photos()->findOrFail($photoId);
        
        $path = str_replace('/storage/', '', parse_url($photo->photo_url, PHP_URL_PATH));
        Storage::disk('public')->delete($path);
        
        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Photo deleted successfully'
        ]);
    }
}