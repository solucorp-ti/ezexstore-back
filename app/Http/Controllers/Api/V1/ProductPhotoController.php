<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Product\StoreProductPhotoRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @group Product Photos
 *
 * APIs for managing product photos
 */
class ProductPhotoController extends BaseApiController
{
    /**
     * List Product Photos
     *
     * Retrieves a paginated list of photos for a specific product.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @queryParam per_page integer The number of photos per page. Default: 15. Example: 10
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "photo_url": "/storage/products/1/photo1.jpg",
     *       "created_at": "2024-12-18T00:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "last_page": 3,
     *     "per_page": 15,
     *     "total": 45
     *   },
     *   "message": "Product photos retrieved successfully"
     * }
     */
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

    /**
     * Upload Product Photos
     *
     * Uploads one or more photos for a specific product.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @bodyParam photos array required An array of photo files to upload. Example: [photo1.jpg, photo2.jpg]
     *
     * @response status=201 scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "photo_url": "/storage/products/1/photo1.jpg",
     *       "created_at": "2024-12-18T00:00:00.000000Z"
     *     }
     *   ],
     *   "message": "Photos uploaded successfully"
     * }
     *
     * @response status=422 scenario="validation error" {
     *   "success": false,
     *   "message": "The photos field is required."
     * }
     */
    public function store(StoreProductPhotoRequest $request, Product $product): JsonResponse
    {
        $photos = collect($request->file('photos'))->map(function ($photo) use ($product) {
            $path = $photo->store("products/$product->id", 'public');

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

    /**
     * Delete Product Photo
     *
     * Deletes a specific photo for the given product.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @urlParam photoId integer required The ID of the photo to delete. Example: 1
     *
     * @response scenario="success" {
     *   "success": true,
     *   "message": "Photo deleted successfully"
     * }
     *
     * @response status=404 scenario="photo not found" {
     *   "success": false,
     *   "message": "Photo not found"
     * }
     */
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
