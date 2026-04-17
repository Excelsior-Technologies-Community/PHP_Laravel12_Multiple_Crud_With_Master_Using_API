<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET: All Products
   public function index(Request $request)
{
    // dynamic per page (default = 3)
    $perPage = $request->per_page ?? 3;

    $products = Product::with('category')->paginate($perPage);

    $data = $products->getCollection()->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $product->quantity,
            'category_id' => $product->category_id,
            'category_name' => $product->category->name ?? null,
            'stock_status' => $product->quantity > 0 ? 'In Stock' : 'Out of Stock'
        ];
    });

    return response()->json([
        'current_page' => $products->currentPage(),
        'last_page' => $products->lastPage(),
        'per_page' => $products->perPage(),
        'total' => $products->total(),
        'data' => $data
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

        $product = Product::create($request->all());

        return response()->json([
            'message' => 'Product Created Successfully',
            'data' => $product
        ], 201);
    }

    // GET: Single Product
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        $result = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $product->quantity,
            'category_id' => $product->category_id,
            'category_name' => $product->category->name ?? null,
            'stock_status' => $product->quantity > 0 ? 'In Stock' : 'Out of Stock'
        ];

        return response()->json($result);
    }

    // POST: Update Product
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required',
            'price'    => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        $product = Product::findOrFail($id);

        $product->update($request->all());

        return response()->json([
            'message' => 'Product Updated Successfully',
            'data' => $product
        ]);
    }

    // POST: Delete Product
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Product Deleted Successfully'
        ]);
    }

    // SEARCH + FILTER PRODUCTS
    public function search(Request $request)
    {
        $query = Product::with('category');

        // Search by name
        if ($request->name) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        // Filter by category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by price range
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'category_id' => $product->category_id,
                'category_name' => $product->category->name ?? null,
                'stock_status' => $product->quantity > 0 ? 'In Stock' : 'Out of Stock'
            ];
        });

        return response()->json($products);
    }
}