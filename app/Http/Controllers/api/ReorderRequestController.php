<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ReorderRequest;
use Illuminate\Http\Request;

class ReorderRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reorderRequest = ReorderRequest::all();

        return response()->json($reorderRequest);
    }

    public function userReorderIndex(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $userId = $request->user()->id;

        $query = ReorderRequest::where(function ($query) use ($userId) {
            $query->where('vendor_id', $userId)
                ->orWhere('store_id', $userId);
        })->orderBy('created_at', 'desc');

        $reorderRequest = $query->get();

        return response()->json($reorderRequest);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer',
            'status' => 'required|string',
            'shipped_date' => 'required|date',
            'delivered_date' => 'required|date',
            'store_id' => 'required|integer',
            'vendor_id' => 'required|integer',
            'product_id' => 'required|integer',
        ]);

        $product = ReorderRequest::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Reorder request created successfully.',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reorderRequest = ReorderRequest::find($id);

        if (!$reorderRequest) {
            return response()->json(['error' => 'Reorder request not found'], 404);
        }

        return response()->json($reorderRequest);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $reorderRequest = ReorderRequest::find($id);

        if (!$reorderRequest) {
            return response()->json(['error' => 'Reorder request not found'], 404);
        }

        $validatedData = $request->validate([
            'quantity' => 'nullable|integer',
            'status' => 'string',
            'shipped_date' => 'nullable|date',
            'delivered_date' => 'nullable|date',
            'vendor_id' => 'nullable|integer',
            'product_id' => 'nullable|integer',
        ]);

        $reorderRequest->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Reorder request updated successfully.',
            'data' => $reorderRequest
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reorderRequest = ReorderRequest::find($id);

        if (!$reorderRequest) {
            return response()->json(['error' => 'Reorder request not found'], 404);
        }

        $reorderRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reorder request deleted successfully.'
        ], 200);
    }
}
