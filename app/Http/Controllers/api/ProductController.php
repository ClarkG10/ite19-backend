<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use PHPUnit\Framework\Reorderable;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::all();

        return response()->json($product);
    }

    public function vendorProductIndex(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $userId = $request->user()->id;

        $inventory = Product::where('vendor_id', $userId)->get();

        return response()->json($inventory);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'UPC' => 'nullable|unique:App\Models\Product',
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'default_price' => 'required|integer|min:1',
            'description' => 'required|string',
            'required|image|mimes:jpeg,png,jpg,gif|max:5048',
            'is_active' => 'required|boolean',
            'vendor_id' => 'required|integer',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/products', 'public');
            $validatedData['image'] = $imagePath;
        }

        $validatedData['is_active'] = $validatedData['is_active'] ?? true;
        $validatedData['default_price'] = $validatedData['default_price'] ?? 1;

        $product = Product::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validatedData = $request->validate([
            'UPC' => 'nullable|unique:App\Models\Product',
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'default_price' => 'nullable|integer|min:1',
            'description' => 'required|string',
            'image_path' =>  'required|image|mimes:jpeg,png,jpg,gif|max:5048',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/products', 'public');
            $validatedData['image'] = $imagePath;
        }

        $validatedData['is_active'] = $validatedData['is_active'] ?? true;
        $validatedData['default_price'] = $validatedData['default_price'] ?? 1;

        $product->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Product Updated successfully.',
            'data' => $product
        ], 200);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 204);
    }
}
