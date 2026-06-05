<?php
// app/Http/Resources/ProductResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'price_formatted' => '₹' . number_format($this->price, 2),
            'quantity' => $this->quantity,
            'stock_status' => $this->quantity > 0 ? 'In Stock' : 'Out of Stock',
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'size' => [
                'id' => $this->size?->id,
                'name' => $this->size?->name,
                'code' => $this->size?->code,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}