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
        $userId = $request->user()->id;

        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $perPage = $request->query('per_page', 10);

        $reorderRequest = ReorderRequest::where(function ($query) use ($userId) {
            $query->where('vendor_id', $userId)
                ->orWhere('store_id', $userId);
        })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

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
            'shipped_date' => 'nullable|date',
            'delivered_date' => 'nullable|date',
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
        $reorderRequest = ReorderRequest::findOrFail($id);

        if (!$reorderRequest) {
            return response()->json(['error' => 'Reorder request not found'], 404);
        }

        return response()->json($reorderRequest);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateQuantity(Request $request, string $id)
    {
        $reorderRequest = ReorderRequest::findOrFail($id);

        if (!$reorderRequest) {
            return response()->json(['error' => 'Reorder request not found'], 404);
        }

        $validatedData = $request->validate([
            'quantity' => 'nullable|integer',
        ]);

        $reorderRequest->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Reorder request updated successfully.',
            'data' => $reorderRequest
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $reorderRequest = ReorderRequest::findOrFail($id);

        if (!$reorderRequest) {
            return response()->json(['error' => 'Reorder request not found'], 404);
        }

        $validatedData = $request->validate([
            'status' => 'string',
            'shipped_date' => 'nullable|date',
            'delivered_date' => 'nullable|date',
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
        $reorderRequest = ReorderRequest::findOrFail($id);

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
