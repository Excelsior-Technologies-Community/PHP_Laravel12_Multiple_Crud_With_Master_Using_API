<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'category_id',
        'size_id',
        'name',
        'price',
        'quantity'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Product belongs to category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    // Product belongs to size
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
    
    // Check if product is in stock
    public function isInStock()
    {
        return $this->quantity > 0;
    }
    
    // Reduce quantity when sold
    public function reduceQuantity($amount = 1)
    {
        if ($this->quantity >= $amount) {
            $this->decrement('quantity', $amount);
            return true;
        }
        return false;
    }
}