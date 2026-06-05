<?php
// app/Http/Controllers/Api/SizeController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Size;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $sizes = Size::withCount('products')->get();
        return $this->successResponse($sizes, 'Sizes retrieved successfully');
    }
    
    public function list()
    {
        $sizes = Size::all(['id', 'name', 'code']);
        return $this->successResponse($sizes, 'Sizes list retrieved');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:sizes,name',
            'code' => 'nullable|string|max:10'
        ]);
        
        $size = Size::create($request->all());
        return $this->successResponse($size, 'Size created successfully', 201);
    }

    public function show($id)
    {
        $size = Size::with('products')->findOrFail($id);
        return $this->successResponse($size, 'Size retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $size = Size::findOrFail($id);
        
        $request->validate([
            'name' => 'required|unique:sizes,name,' . $id,
            'code' => 'nullable|string|max:10'
        ]);
        
        $size->update($request->all());
        return $this->successResponse($size, 'Size updated successfully');
    }

    public function destroy($id)
    {
        $size = Size::findOrFail($id);
        
        if ($size->products()->count() > 0) {
            return $this->errorResponse('Cannot delete size with associated products', null, 422);
        }
        
        $size->delete();
        return $this->successResponse(null, 'Size deleted successfully');
    }
}