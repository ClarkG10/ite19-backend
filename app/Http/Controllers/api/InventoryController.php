<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventory = Inventory::all();

        return response()->json($inventory);
    }

    public function storeInventoryIndex(Request $request)
    {
        $userId = $request->user()->id;

        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $perPage = $request->query('per_page', 10);

        $inventory = Inventory::where('store_id', $userId)
            ->paginate($perPage);

        if ($inventory->isEmpty()) {
            return response()->json(['message' => 'No inventory found'], 200);
        }

        return response()->json($inventory);
    }

    public function storeInventoryall(Request $request)
    {
        $userId = $request->user()->id;

        // Check if the user is authenticated
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Retrieve the inventory for the authenticated user
        $inventory = Inventory::where('store_id', $userId)->get();

        // Check if inventory is empty
        if ($inventory->isEmpty()) {
            return response()->json(['message' => 'No inventory found'], 200);
        }

        // Return the inventory data
        return response()->json($inventory);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1',
            'new_price' => 'required|numeric|min:1',
            'reorder_level' => 'required|integer|min:1',
            'reorder_quantity' => 'required|integer|min:1',
            'store_id' => 'required|integer',
            'product_id' => 'required|integer',
            'order_type' => 'required|string',
            'auto_order_quantity' => 'required|integer|min:1',
        ]);

        $product = Inventory::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Inventory created successfully.',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        return response()->json($inventory);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $inventory = Inventory::findOrFail($id);

        if (!$inventory) {
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        $validatedData = $request->validate([
            'quantity' => 'integer|min:1',
            'new_price' => 'numeric|min:1',
            'reorder_level' => 'integer|min:1',
            'reorder_quantity' => 'integer|min:1',
            'order_type' => 'string',
            'auto_order_quantity' => 'integer|min:1',
        ]);

        $inventory->update($validatedData);

        return response()->json($inventory);
    }

    public function updateStatus(Request $request, $id)
    {
        $validatedData = $request->validate([
            'is_reordered' => 'required|boolean',
        ]);

        $inventory = Inventory::findOrFail($id);
        $inventory->is_reordered = $validatedData['is_reordered'];
        $inventory->save();

        return response()->json(['message' => 'Status updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        $inventory->delete();

        return response()->json(['message' => 'Inventory deleted successfully']);
    }
}
