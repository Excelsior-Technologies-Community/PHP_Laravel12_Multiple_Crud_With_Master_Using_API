<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET: All Products
  public function index()
    {
        $products = Product::with('category')->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'category_id' => $product->category_id,
                'category_name' => $product->category->name ?? null, // single field
            ];
        });

        return response()->json($products);
    }



    // POST: Create Product
    public function store(Request $request)
    {
        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    // GET: Single Product

 public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        $result = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'category_id' => $product->category_id,
            'category_name' => $product->category->name ?? null,
        ];

        return response()->json($result);
    }
    // PUT: Update Product
   // POST: Update Product (instead of PUT)
public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $product->update($request->all());

    return response()->json($product);
}

// POST: Delete Product (instead of DELETE)
public function destroy($id)
{
    Product::findOrFail($id)->delete();
    return response()->json(['message' => 'Product Deleted']);
}

}
