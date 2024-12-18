<?php

namespace App\Http\Controllers\Api;

use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseApiController
{
    protected $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->getProducts($request->tenant->id);
        return $this->successResponse($products, 'Products retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,discontinued',
            'unit_of_measure' => 'required|in:piece,kg,liter,meter',
            'sku' => 'nullable|string|unique:products,sku',
            'part_number' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'part_condition' => 'nullable|in:new,used,discontinued,damaged,refurbished',
            'brand' => 'nullable|string',
            'family' => 'nullable|string',
            'line' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $product = $this->productService->createProduct($request->all(), $request->tenant->id);
        return $this->successResponse($product, 'Product created successfully', 201);
    }

    public function show(Request $request, $id)
    {
        $product = $this->productService->findProduct($id, $request->tenant->id);
        
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse($product, 'Product retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:active,inactive,discontinued',
            'unit_of_measure' => 'sometimes|in:piece,kg,liter,meter',
            'sku' => 'nullable|string|unique:products,sku,' . $id,
            'part_number' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'part_condition' => 'nullable|in:new,used,discontinued,damaged,refurbished',
            'brand' => 'nullable|string',
            'family' => 'nullable|string',
            'line' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $product = $this->productService->updateProduct($id, $request->all(), $request->tenant->id);

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse($product, 'Product updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        if (!$this->productService->deleteProduct($id, $request->tenant->id)) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse(null, 'Product deleted successfully');
    }
}