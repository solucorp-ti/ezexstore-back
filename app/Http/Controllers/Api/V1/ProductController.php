<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @group Products
 *
 * APIs for managing products in the tenant's catalog
 */
class ProductController extends BaseApiController
{
    protected $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }
    /**
     * List Products
     *
     * Returns a list of all products belonging to the authenticated tenant.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "product_serial": "TEST001",
     *       "product_name": "Test Product",
     *       "description": "Product description",
     *       "base_price": "99.99",
     *       "status": "active",
     *       "unit_of_measure": "piece",
     *       "sku": "SKU001",
     *       "part_number": "PART001",
     *       "serial_number": "SN001",
     *       "part_condition": "new",
     *       "brand": "Test Brand",
     *       "family": "Test Family",
     *       "line": "Test Line",
     *       "is_active": true
     *     }
     *   ],
     *   "message": "Products retrieved successfully"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->getProducts(
            $request->tenant->id,
            $request->input('per_page')
        );

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total()
            ],
            'message' => 'Products retrieved successfully'
        ]);
    }

    private function getValidationRules($productId = null): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,discontinued',
            'unit_of_measure' => 'required|in:piece,kg,liter,meter',
            'sku' => [
                'nullable',
                'string',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('tenant_id', request()->tenant->id);
                })->ignore($productId)
            ],
            'part_number' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'part_condition' => 'nullable|in:new,used,discontinued,damaged,refurbished',
            'brand' => 'nullable|string',
            'family' => 'nullable|string',
            'line' => 'nullable|string',
        ];
    }

    /**
     * Create Product
     *
     * Creates a new product for the authenticated tenant.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @bodyParam product_name string required The name of the product. Example: Test Product
     * @bodyParam description string optional The description of the product. Example: A test product description
     * @bodyParam base_price numeric required The base price of the product. Example: 99.99
     * @bodyParam status string required The status of the product (active, inactive, discontinued). Example: active
     * @bodyParam unit_of_measure string required The unit of measure (piece, kg, liter, meter). Example: piece
     * @bodyParam sku string optional The SKU of the product (must be unique per tenant). Example: SKU001
     * @bodyParam part_number string optional The part number of the product. Example: PART001
     * @bodyParam serial_number string optional The serial number of the product. Example: SN001
     * @bodyParam part_condition string optional The condition of the product (new, used, discontinued, damaged, refurbished). Example: new
     * @bodyParam brand string optional The brand of the product. Example: Test Brand
     * @bodyParam family string optional The product family. Example: Test Family
     * @bodyParam line string optional The product line. Example: Test Line
     *
     * @response scenario="success" status=201 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "product_name": "Test Product",
     *     "description": "A test product description",
     *     "base_price": "99.99",
     *     "status": "active",
     *     "unit_of_measure": "piece",
     *     "sku": "SKU001",
     *     "created_at": "2024-12-18T00:00:00.000000Z",
     *     "updated_at": "2024-12-18T00:00:00.000000Z"
     *   },
     *   "message": "Product created successfully"
     * }
     *
     * @response status=422 scenario="validation error" {
     *   "success": false,
     *   "message": "The product name field is required."
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getValidationRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $product = $this->productService->createProduct($request->all(), $request->tenant->id);
        return $this->successResponse($product, 'Product created successfully', 201);
    }

    /**
     * Get Product Details
     *
     * Returns detailed information about a specific product.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @urlParam id integer required The ID of the product. Example: 1
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "product_serial": "TEST001",
     *     "product_name": "Test Product",
     *     "description": "Product description",
     *     "base_price": "99.99",
     *     "status": "active",
     *     "unit_of_measure": "piece",
     *     "sku": "SKU001",
     *     "part_number": "PART001",
     *     "serial_number": "SN001",
     *     "part_condition": "new",
     *     "brand": "Test Brand",
     *     "family": "Test Family",
     *     "line": "Test Line",
     *     "is_active": true
     *   },
     *   "message": "Product retrieved successfully"
     * }
     *
     * @response status=404 scenario="not found" {
     *   "success": false,
     *   "message": "Product not found"
     * }
     */
    public function show(Request $request, $id)
    {
        $product = $this->productService->findProduct($id, $request->tenant->id);

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse($product, 'Product retrieved successfully');
    }

    /**
     * Update Product
     *
     * Updates an existing product. All fields are optional for updates.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @urlParam id integer required The ID of the product. Example: 1
     *
     * @bodyParam product_name string optional The name of the product. Example: Updated Product
     * @bodyParam description string optional The description of the product.
     * @bodyParam base_price numeric optional The base price of the product. Example: 149.99
     * @bodyParam status string optional The status (active, inactive, discontinued). Example: inactive
     * @bodyParam unit_of_measure string optional The unit of measure (piece, kg, liter, meter). Example: kg
     * @bodyParam sku string optional The SKU (must be unique per tenant). Example: SKU002
     * @bodyParam part_number string optional The part number. Example: PART002
     * @bodyParam serial_number string optional The serial number. Example: SN002
     * @bodyParam part_condition string optional The condition (new, used, discontinued, damaged, refurbished). Example: used
     * @bodyParam brand string optional The brand. Example: New Brand
     * @bodyParam family string optional The product family. Example: New Family
     * @bodyParam line string optional The product line. Example: New Line
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "product_name": "Updated Product",
     *     "base_price": "149.99",
     *     "status": "inactive"
     *   },
     *   "message": "Product updated successfully"
     * }
     *
     * @response status=404 scenario="not found" {
     *   "success": false,
     *   "message": "Product not found"
     * }
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->getValidationRules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $product = $this->productService->updateProduct($id, $request->all(), $request->tenant->id);

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse($product, 'Product updated successfully');
    }

    /**
     * Delete Product
     *
     * Soft deletes a product. The product can be reactivated later by updating its status to active.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @urlParam id integer required The ID of the product. Example: 1
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": null,
     *   "message": "Product deleted successfully"
     * }
     *
     * @response status=404 scenario="not found" {
     *   "success": false,
     *   "message": "Product not found"
     * }
     */
    public function destroy(Request $request, $id)
    {
        if (!$this->productService->deleteProduct($id, $request->tenant->id)) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse(null, 'Product deleted successfully');
    }
}