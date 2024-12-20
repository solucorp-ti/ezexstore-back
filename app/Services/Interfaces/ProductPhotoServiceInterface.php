<?php

namespace App\Services\Interfaces;

use App\Models\Product;
use Illuminate\Http\UploadedFile;

interface ProductPhotoServiceInterface
{
    public function getPhotos(int $productId);
    public function uploadPhoto(Product $product, UploadedFile $file);
    public function deletePhoto(int $productId, int $photoId);
}