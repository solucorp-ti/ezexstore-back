<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Product;
use App\Services\Interfaces\ProductPhotoServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
/**
 * @group Product Photos
 *
 * APIs for managing product photos
 */
class ProductPhotoController extends BaseApiController
{
    protected $photoService;

    public function __construct(ProductPhotoServiceInterface $photoService)
    {
        $this->photoService = $photoService;
    }
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
    public function index(Product $product)
    {
        $photos = $this->photoService->getPhotos($product->id);
        return $this->successResponse($photos, 'Photos retrieved successfully');
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
    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|file|image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        try {
            $photo = $this->photoService->uploadPhoto($product, $request->file('photo'));
            return $this->successResponse($photo, 'Photo uploaded successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error uploading photo: ' . $e->getMessage(), 500);
        }
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
    public function destroy(Product $product, $photoId)
    {
        try {
            $this->photoService->deletePhoto($product->id, $photoId);
            return $this->successResponse(null, 'Photo deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting photo: ' . $e->getMessage(), 500);
        }
    }
}
