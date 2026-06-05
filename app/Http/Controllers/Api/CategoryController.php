<?php
// app/Http/Controllers/Api/CategoryController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    // GET: All Categories with Pagination
    public function index(Request $request)
    {
        $perPage = $request->per_page ?? 10;
        $categories = Category::withCount('products')->paginate($perPage);
        
        return $this->paginatedResponse($categories);
    }

    // GET: All Categories List (Without Pagination for dropdowns)
    public function list()
    {
        $categories = Category::all(['id', 'name']);
        return $this->successResponse($categories, 'Categories retrieved successfully');
    }

    // POST: Create Category
    public function store(CategoryStoreRequest $request)
    {
        $category = Category::create($request->validated());
        return $this->successResponse(new CategoryResource($category), 'Category created successfully', 201);
    }

    // GET: Single Category with Products
    public function show($id)
    {
        $category = Category::with(['products' => function($query) {
            $query->with(['category', 'size']);
        }])->findOrFail($id);
        
        return $this->successResponse(new CategoryResource($category), 'Category retrieved successfully');
    }

    // POST: Update Category
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $id
        ]);
        
        $category->update($request->only('name'));
        return $this->successResponse(new CategoryResource($category), 'Category updated successfully');
    }

    // POST: Delete Category (Soft Delete)
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return $this->errorResponse('Cannot delete category with existing products', null, 422);
        }
        
        $category->delete();
        return $this->successResponse(null, 'Category deleted successfully');
    }

    // GET: Category with Products (MASTER DETAIL)
    public function categoryProducts($id)
    {
        $category = Category::with(['products' => function($query) {
            $query->with(['size'])->latest();
        }])->findOrFail($id);
        
        return $this->successResponse([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
            'total_products' => $category->products->count(),
            'products' => $category->products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    'size' => $product->size->name ?? 'N/A',
                    'stock_status' => $product->quantity > 0 ? 'In Stock' : 'Out of Stock'
                ];
            })
        ], 'Category products retrieved successfully');
    }
}