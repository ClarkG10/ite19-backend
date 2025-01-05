<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ReorderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // Validate input
        $validated = $request->validate([
            'per_page' => 'nullable|integer|min:1',
            'keyword' => 'nullable|string|max:255',
        ]);

        $perPage = $validated['per_page'] ?? 10;

        // Base query with product and store relationships
        $query = ReorderRequest::where(function ($query) use ($userId) {
            $query->where('vendor_id', $userId)
                ->orWhere('store_id', $userId);
        })
            ->with(['product', 'store']) // Eager load product and store relationships
            ->orderBy('created_at', 'desc');

        // Apply keyword filter if provided
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('quantity', 'like', '%' . $keyword . '%')
                    ->orWhere('status', 'like', '%' . $keyword . '%')
                    ->orWhere('order_type', 'like', '%' . $keyword . '%')
                    ->orWhereHas('product', function ($subQuery) use ($keyword) {
                        $subQuery->where('product_name', 'like', '%' . $keyword . '%')
                            ->orWhere('brand', 'like', '%' . $keyword . '%')
                            ->orWhere('description', 'like', '%' . $keyword . '%')
                            ->orWhere('section_name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('store', function ($subQuery) use ($keyword) {
                        $subQuery->where('business_name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        // Paginate results
        $reorderRequest = $query->paginate($perPage);

        // Check if no data was found
        if ($reorderRequest->isEmpty()) {
            return response()->json(['message' => 'No reorder requests found'], 200);
        }

        // Return the paginated response
        return response()->json($reorderRequest);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer',
            'status' => 'nullable|string',
            'shipped_date' => 'nullable|date',
            'delivered_date' => 'nullable|date',
            'store_id' => 'required|integer',
            'vendor_id' => 'required|integer',
            'product_id' => 'required|integer',
            'order_type' => 'required|string',
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
        // Validate incoming request
        $validatedData = $request->validate([
            'order_type' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        Log::info('Incoming Request:', $validatedData); // Log the validated data

        // Find the reorder request or fail
        $reorderRequest = ReorderRequest::findOrFail($id);

        // Update the reorder request with validated data
        $reorderRequest->update([
            'order_type' => $validatedData['order_type'],
            'quantity' => $validatedData['quantity'],
        ]);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Reorder request updated successfully.',
            'data' => $reorderRequest,
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
