<?php
// app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;

    // GET: All Products with Pagination and Filters
    public function index(Request $request)
    {
        $perPage = $request->per_page ?? 10;
        $query = Product::with(['category', 'size']);
        
        // Apply filters
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->size_id) {
            $query->where('size_id', $request->size_id);
        }
        
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        
        if ($request->search) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }
        
        if ($request->stock_status === 'in_stock') {
            $query->where('quantity', '>', 0);
        } elseif ($request->stock_status === 'out_of_stock') {
            $query->where('quantity', '=', 0);
        }
        
        // Sort
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);
        
        $products = $query->paginate($perPage);
        
        return $this->paginatedResponse($products, 'Products retrieved successfully');
    }

    // POST: Create Product
    public function store(ProductStoreRequest $request)
    {
        $product = Product::create($request->validated());
        return $this->successResponse(new ProductResource($product->load(['category', 'size'])), 'Product created successfully', 201);
    }

    // GET: Single Product
    public function show($id)
    {
        $product = Product::with(['category', 'size'])->findOrFail($id);
        return $this->successResponse(new ProductResource($product), 'Product retrieved successfully');
    }

    // POST: Update Product
    public function update(ProductUpdateRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->validated());
        
        return $this->successResponse(new ProductResource($product->load(['category', 'size'])), 'Product updated successfully');
    }

    // POST: Delete Product (Soft Delete)
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        
        return $this->successResponse(null, 'Product deleted successfully');
    }
    
    // POST: Restore Soft Deleted Product
    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        
        if (!$product->trashed()) {
            return $this->errorResponse('Product is not deleted', null, 400);
        }
        
        $product->restore();
        return $this->successResponse(new ProductResource($product->load(['category', 'size'])), 'Product restored successfully');
    }
    
    // POST: Bulk Delete Products
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);
        
        $deleted = Product::whereIn('id', $request->ids)->delete();
        
        return $this->successResponse([
            'deleted_count' => $deleted
        ], $deleted . ' products deleted successfully');
    }
    
    // POST: Bulk Update Stock
    public function bulkUpdateStock(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|exists:products,id',
            'updates.*.quantity' => 'required|integer|min:0'
        ]);
        
        $updated = 0;
        foreach ($request->updates as $update) {
            $product = Product::find($update['id']);
            $product->quantity = $update['quantity'];
            $product->save();
            $updated++;
        }
        
        return $this->successResponse([
            'updated_count' => $updated
        ], 'Stock updated successfully');
    }
    
    // GET: Advanced Search
    public function search(Request $request)
    {
        $query = Product::with(['category', 'size']);
        
        if ($request->name) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->size_id) {
            $query->where('size_id', $request->size_id);
        }
        
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        
        $products = $query->latest()->get();
        
        return $this->successResponse(ProductResource::collection($products), 'Search results retrieved');
    }
    
    // POST: Reduce Product Quantity (For sales)
    public function reduceQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        
        $product = Product::findOrFail($id);
        
        if ($product->reduceQuantity($request->quantity)) {
            return $this->successResponse([
                'product' => new ProductResource($product),
                'remaining_stock' => $product->quantity
            ], 'Stock reduced successfully');
        }
        
        return $this->errorResponse('Insufficient stock available', null, 400);
    }
}