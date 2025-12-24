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
        $products = Product::with('category')->get();

        return response()->json([
            'status' => true,
            'data' => $products->map(function ($product) {
                return [
                    'id'            => $product->id,
                    'name'          => $product->name,
                    'price'         => $product->price,
                    'quantity'      => $product->quantity,
                    'category_id'   => $product->category_id,
                    'category_name' => $product->category->name ?? null,
                ];
            })
        ]);
    }

    // POST: Create Product
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required',
            'price'       => 'required|numeric',
            'quantity'    => 'required|numeric',
        ]);

        $product = Product::create([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'price'       => $request->price,
            'quantity'    => $request->quantity,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    // GET: Single Product
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => [
                'id'            => $product->id,
                'name'          => $product->name,
                'price'         => $product->price,
                'quantity'      => $product->quantity,
                'category_id'   => $product->category_id,
                'category_name' => $product->category->name ?? null,
            ]
        ]);
    }

    // POST: Update Product
    public function update(Request $request, $id)
    {
        $request->validate([
          
            'name'        => 'required',
            'price'       => 'required|numeric',
            'quantity'    => 'required|numeric',
        ]);

        $product = Product::findOrFail($id);

        $product->update([
        
            'name'        => $request->name,
            'price'       => $request->price,
            'quantity'    => $request->quantity,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    // POST: Delete Product
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
