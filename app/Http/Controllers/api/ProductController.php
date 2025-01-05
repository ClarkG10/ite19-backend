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

    public function listtAllProducts(Request $request)
    {
        $perPage = $request->query('per_page', 20);
        $keyword = $request->query('keyword', null); // Get the search keyword

        $query = Product::query();

        // Apply search filter if a keyword is provided
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('product_name', 'like', "%{$keyword}%")
                    ->orWhere('product_type', 'like', "%{$keyword}%")
                    ->orWhere('brand', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        $products = $query->paginate($perPage);

        return response()->json($products);
    }

    public function vendorProductIndex(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $userId = $request->user()->id;
        $perPage = $request->query('per_page', 10);
        $keyword = $request->query('keyword', null); // Get the search keyword

        $query = Product::where('vendor_id', $userId);

        // Apply search filter if a keyword is provided
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('product_name', 'like', "%{$keyword}%")
                    ->orWhere('product_type', 'like', "%{$keyword}%")
                    ->orWhere('brand', 'like', "%{$keyword}%");
            });
        }

        $products = $query->paginate($perPage);

        return response()->json($products);
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
            'selling_price' => 'nullable|numeric|min:1',
            'cost_price' => 'nullable|numeric|min:1',
            'wholesale_price' => 'nullable|numeric|min:1',
            'section_name' => 'nullable|string|max:255',
            'stock_quantity' => 'nullable|integer|min:1',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
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
        // Find the product or return 404 if not found
        $product = Product::findOrFail($id);

        // Validate the incoming data
        $validatedData = $request->validate([
            'product_name'   => 'required|string|max:255',
            'product_type'   => 'required|string|max:255',
            'brand'          => 'required|string|max:255',
            'section_name'   => 'nullable|string|max:255',
            'selling_price'  => 'required|numeric|min:1',
            'cost_price'     => 'required|numeric|min:1',
            'wholesale_price' => 'required|numeric|min:1',
            'description'    => 'nullable|string',
            'stock_quantity' => 'required|integer|min:1',
            'image_path'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'status'         => 'required|string',
            'is_active'     => 'nullable|boolean',
        ]);

        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('images/products', 'public');
            $validatedData['image_path'] = $imagePath;
        }

        $product->update($validatedData);

        // Respond with success
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data'    => $product
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
        $validatedData = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $product = Product::findOrFail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->is_active = $validatedData['is_active'];
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
