<?php
// app/Http/Controllers/Api/DashboardController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Traits\ApiResponseTrait;

class DashboardController extends Controller
{
    use ApiResponseTrait;
    
    public function stats()
    {
        $stats = [
            'total_categories' => Category::count(),
            'total_products' => Product::count(),
            'total_sizes' => Size::count(),
            'total_stock_value' => Product::sum(\DB::raw('price * quantity')),
            'total_products_in_stock' => Product::where('quantity', '>', 0)->count(),
            'total_products_out_of_stock' => Product::where('quantity', '=', 0)->count(),
            'recent_products' => Product::with(['category', 'size'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $product->quantity,
                        'category' => $product->category->name ?? null,
                    ];
                }),
            'category_wise_product_count' => Category::withCount('products')
                ->get()
                ->map(function($category) {
                    return [
                        'category' => $category->name,
                        'product_count' => $category->products_count
                    ];
                })
        ];
        
        return $this->successResponse($stats, 'Dashboard statistics retrieved');
    }
}