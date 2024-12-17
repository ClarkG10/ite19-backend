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
        // Validate the input
        $validated = $request->validate([
            'per_page' => 'nullable|integer|min:1',
            'keyword' => 'nullable|string|max:255',
        ]);

        // Get the authenticated user's ID
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $userId = $user->id;
        $perPage = $validated['per_page'] ?? 10;

        // Base query with filters
        $saleQuery = Sale::where('store_id', $userId)
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('quantity', 'like', $keyword)
                        ->orWhere('payment_method', 'like', '%' . $keyword . '%')
                        ->orWhere('total_amount', 'like',  $keyword)
                        ->orWhere('price', 'like',  $keyword);
                });
            });

        // Paginate the results
        $sales = $saleQuery->paginate($perPage);

        // Return the paginated response
        return response()->json($sales);
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
