<?php
// app/Models/Size.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = ['name', 'code'];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Size has many products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}