<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Sizes
        $sizes = [
            ['name' => 'Small', 'code' => 'S'],
            ['name' => 'Medium', 'code' => 'M'],
            ['name' => 'Large', 'code' => 'L'],
            ['name' => 'Extra Large', 'code' => 'XL'],
            ['name' => 'One Size', 'code' => 'OS'],
        ];
        
        foreach ($sizes as $size) {
            Size::create($size);
        }
        
        // Create Categories
        $categories = [
            ['name' => 'Electronics'],
            ['name' => 'Clothing'],
            ['name' => 'Books'],
            ['name' => 'Home & Garden'],
            ['name' => 'Sports'],
        ];
        
        foreach ($categories as $category) {
            Category::create($category);
        }
        
        // Create Products
        $products = [
            ['category_id' => 1, 'size_id' => 5, 'name' => 'Smartphone', 'price' => 49999, 'quantity' => 50],
            ['category_id' => 1, 'size_id' => 5, 'name' => 'Laptop', 'price' => 65000, 'quantity' => 30],
            ['category_id' => 1, 'size_id' => 5, 'name' => 'Headphones', 'price' => 2999, 'quantity' => 100],
            ['category_id' => 2, 'size_id' => 1, 'name' => 'T-Shirt', 'price' => 999, 'quantity' => 200],
            ['category_id' => 2, 'size_id' => 2, 'name' => 'Jeans', 'price' => 1999, 'quantity' => 150],
            ['category_id' => 3, 'size_id' => 5, 'name' => 'Programming Book', 'price' => 599, 'quantity' => 75],
            ['category_id' => 4, 'size_id' => 5, 'name' => 'Coffee Mug', 'price' => 299, 'quantity' => 500],
            ['category_id' => 5, 'size_id' => 3, 'name' => 'Football', 'price' => 1499, 'quantity' => 60],
        ];
        
        foreach ($products as $product) {
            Product::create($product);
        }
    }
}