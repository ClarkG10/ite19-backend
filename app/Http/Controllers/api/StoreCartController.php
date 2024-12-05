<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\StoreCart;
use Illuminate\Http\Request;

class StoreCartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $storecart = StoreCart::all();

        return response()->json($storecart);
    }

    public function storeCartIndex(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $userId = $request->user()->id;

        $storecart = StoreCart::where('store_id', $userId)->get();

        return response()->json($storecart);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'store_id' => 'required|integer',
            'vendor_id' => 'required|integer',
        ]);

        $storecart = StoreCart::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Successfully added to cart.',
            'data' => $storecart
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $storecart = StoreCart::findOrFail($id);

        if (!$storecart) {
            return response()->json(['error' => 'Store cart not found'], 404);
        }

        return response()->json($storecart);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateQuantity(Request $request, string $id)
    {
        $storecart = StoreCart::findOrFail($id);

        if (!$storecart) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $validatedData = $request->validate([
            'quantity' => 'integer',
        ]);

        $storecart->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated cart item quantity.',
            'data' => $storecart
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
