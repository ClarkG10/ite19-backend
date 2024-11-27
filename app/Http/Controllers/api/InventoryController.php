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
        if (!$request->user()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $userId = $request->user()->id;

        $inventory = Inventory::where('store_id', $userId)->get();

        return response()->json($inventory);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1',
            'new_price' => 'required|integer|min:1',
            'reorder_level' => 'required|integer',
            'reorder_quantity' => 'required|integer',
            'store_id' => 'required|integer',
            'product_id' => 'required|integer',
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
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        $validatedData = $request->validate([
            'quantity' => 'integer|min:1',
            'new_price' => 'integer|min:1',
            'reorder_level' => 'integer|min:1',
            'reorder_quantity' => 'integer|min:1',
        ]);

        $inventory->update($validatedData);

        return response()->json($inventory);
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
