<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller {
    public function index() {
        return response()->json(Size::all());
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|unique:sizes,name']);
        $size = Size::create($request->all());
        return response()->json($size, 201);
    }

    public function show($id) {
        return response()->json(Size::findOrFail($id));
    }

    public function update(Request $request, $id) {
        $size = Size::findOrFail($id);
        $size->update($request->all());
        return response()->json($size);
    }

    public function destroy($id) {
        Size::findOrFail($id)->delete();
        return response()->json(['message' => 'Size Deleted']);
    }
}