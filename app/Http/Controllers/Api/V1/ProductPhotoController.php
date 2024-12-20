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
     * Retrieves list of photos for a specific product.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @urlParam product integer required The ID of the product. Example: 1
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "product_id": 1,
     *       "photo_url": "products/1/1/1_1734660820.png",
     *       "created_at": "2024-12-20T02:13:40.000000Z",
     *       "updated_at": "2024-12-20T02:13:40.000000Z",
     *       "full_url": "http://localhost:8000/storage/products/1/1/1_1734660820.png"
     *     }
     *   ],
     *   "message": "Photos retrieved successfully"
     * }
     * 
     * @response status=404 scenario="product not found" {
     *   "success": false,
     *   "message": "Product not found"
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
     * Uploads one or more photos for a specific product. Send multiple photos using the same form-field name with array notation.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @bodyParam photos[] file[] required Array of photo files to upload (max 2MB each, formats: jpg, jpeg, png, webp). Example: photos[]=photo1.jpg&photos[]=photo2.jpg
     *
     * @response status=201 scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "product_id": 1,
     *       "photo_url": "products/1/1/1_1734660820.png",
     *       "created_at": "2024-12-20T02:13:40.000000Z",
     *       "updated_at": "2024-12-20T02:13:40.000000Z",
     *       "full_url": "http://localhost:8000/storage/products/1/1/1_1734660820.png"
     *     }
     *   ],
     *   "message": "Photos uploaded successfully"
     * }
     *
     * @response status=422 scenario="validation error" {
     *   "success": false,
     *   "message": "The photos field is required."
     * }
     * 
     * @response status=422 scenario="invalid file" {
     *   "success": false,
     *   "message": "The photo must be an image file (jpg, jpeg, png, webp)."
     * }
     * 
     * @response status=422 scenario="file too large" {
     *   "success": false,
     *   "message": "The photo may not be greater than 2MB."
     * }
     */
    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'photos' => 'required|array',
            'photos.*' => 'required|file|image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        try {
            $uploadedPhotos = [];
            foreach ($request->file('photos') as $photo) {
                $uploadedPhotos[] = $this->photoService->uploadPhoto($product, $photo);
            }

            return $this->successResponse($uploadedPhotos, 'Photos uploaded successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error uploading photos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete Product Photo
     *
     * Deletes a specific photo for the given product. This action cannot be undone.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @urlParam product integer required The ID of the product. Example: 1
     * @urlParam photoId integer required The ID of the photo to delete. Example: 1
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": null,
     *   "message": "Photo deleted successfully"
     * }
     *
     * @response status=404 scenario="not found" {
     *   "success": false,
     *   "message": "Photo not found"
     * }
     * 
     * @response status=403 scenario="unauthorized" {
     *   "success": false,
     *   "message": "This action is unauthorized."
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
