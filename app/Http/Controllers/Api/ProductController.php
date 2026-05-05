<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->per_page ?? 3;

        $products = Product::with(['category', 'size'])->paginate($perPage);

        $data = $products->getCollection()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'category_id' => $product->category_id,
                'category_name' => $product->category->name ?? null,
                'size_id' => $product->size_id,
                'size_name' => $product->size->name ?? null,
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

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'size_id'     => 'required|exists:sizes,id',
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

    public function show($id)
    {
        $product = Product::with(['category', 'size'])->findOrFail($id);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $product->quantity,
            'category_id' => $product->category_id,
            'category_name' => $product->category->name ?? null,
            'size_id' => $product->size_id,
            'size_name' => $product->size->name ?? null,
            'stock_status' => $product->quantity > 0 ? 'In Stock' : 'Out of Stock'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'exists:categories,id',
            'size_id'     => 'exists:sizes,id',
            'name'        => 'string',
            'price'       => 'numeric',
            'quantity'    => 'numeric',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return response()->json([
            'message' => 'Product Updated Successfully',
            'data' => $product
        ]);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Product Deleted Successfully'
        ]);
    }

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

        $products = $query->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'category_name' => $product->category->name ?? null,
                'size_name' => $product->size->name ?? null,
                'stock_status' => $product->quantity > 0 ? 'In Stock' : 'Out of Stock'
            ];
        });

        return response()->json($products);
    }
}