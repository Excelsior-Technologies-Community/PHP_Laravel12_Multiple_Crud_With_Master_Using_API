<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET: All Categories
    public function index()
    {
        return response()->json(Category::all());
    }

    // POST: Create Category
    public function store(Request $request)
    {
        $category = Category::create([
            'name' => $request->name
        ]);

        return response()->json($category, 201);
    }

    // GET: Single Category
    public function show($id)
    {
        return response()->json(Category::findOrFail($id));
    }

    // PUT: Update Category
// POST: Update Category (instead of PUT)
public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);
    $category->update($request->all());

    return response()->json($category);
}

// POST: Delete Category (instead of DELETE)
public function destroy($id)
{
    Category::findOrFail($id)->delete();
    return response()->json(['message' => 'Category Deleted']);
}

    // GET: Category with Products (MASTER)
    public function categoryProducts($id)
    {
        $category = Category::with('products')->findOrFail($id);
        return response()->json($category);
    }

    public function allProductsWithCategory()
{
    // Get all products with their category using Eloquent relationship
    $products = \App\Models\Product::with('category')->get();

    // Format response nicely
    $data = $products->map(function($product) {
        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => $product->price,
            'category_id' => $product->category->id ?? null,
            'category_name' => $product->category->name ?? null,
        ];
    });

    return response()->json([
        'total' => $data->count(),
        'products' => $data
    ]);
}

}
