<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sale = Sale::all();

        return response()->json($sale);
    }

    public function storeSaleIndex(Request $request)
    {
        $userId = $request->user()->id;

        if (!$request->user()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $perPage = $request->query('per_page', 10);

        $sale = Sale::where('store_id', $userId)
            ->paginate($perPage);

        return response()->json($sale);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer',
            'price' => 'required|numeric|min:1',
            'total_amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'store_id' => 'required|integer',
            'product_id' => 'required|integer',
            'customer_id' => 'required|integer',
        ]);

        $sale = Sale::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Sale created successfully.',
            'data' => $sale
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sale = Sale::findOrFail($id);

        if (!$sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        return response()->json($sale);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sale = Sale::findOrFail($id);

        if (!$sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        $validatedData = $request->validate([
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'product_id' => 'required|integer',
            'customer_id' => 'required|integer',
        ]);

        $sale->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Sale updated successfully.',
            'data' => $sale
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sale = Sale::findOrFail($id);

        if (!$sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        $sale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sale deleted successfully.'
        ]);
    }
}
