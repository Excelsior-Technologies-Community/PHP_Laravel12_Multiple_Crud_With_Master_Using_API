<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['name'];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // One category has many products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    // Get active products only (not soft deleted)
    public function activeProducts()
    {
        return $this->hasMany(Product::class)->whereNull('deleted_at');
    }
    
    // Get product count
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }
}