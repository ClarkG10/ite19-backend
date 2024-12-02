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
        $userId = $request->user()->id;

        if (!$request->user()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $perPage = $request->query('per_page', 10);

        $product = Product::where('store_id', $userId)
            ->paginate($perPage);

        return response()->json($product);
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
            'selling_price' => 'nullable|integer|min:1',
            'cost_price' => 'nullable|integer|min:1',
            'stock_quantity' => 'nullable|integer|min:1',
            'status' => 'nullable|string',
            'description' => 'required|string',
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:5048',
            'is_active' => 'nullable|boolean',
            'vendor_id' => 'required|integer',
        ]);

        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('images/products', 'public');
            $validatedData['image_path'] = $imagePath;
        }

        $validatedData['is_active'] = $validatedData['is_active'] ?? true;

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
        $product = Product::findOrFail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateDetails(Request $request, string $id)
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
            'selling_price' => 'nullable|integer|min:1',
            'cost_price' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'image_path' =>  'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('images/products', 'public');
            $validatedData['image_path'] = $imagePath;
        }

        $product->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Product Updated successfully.',
            'data' => $product
        ], 200);
    }

    public function updateStatus(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validatedData = $request->validate([
            'status' => 'nullable|string',
        ]);

        $product->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Product Updated Status successfully.',
            'data' => $product
        ], 200);
    }

    public function updateIsActive(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validatedData = $request->validate([
            'is_active' => 'nullable|boolean',
        ]);

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
        $product = Product::findOrFail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 204);
    }
}
