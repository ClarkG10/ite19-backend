<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Inventory;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Orders::all();
    }

    public function userOrderIndex(Request $request)
    {
        $userId = $request->user()->id;

        // Check if the user is authenticated
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Validate input
        $validated = $request->validate([
            'per_page' => 'nullable|integer|min:1',
            'keyword' => 'nullable|string|max:255',
        ]);

        $perPage = $validated['per_page'] ?? 10;

        // Base query with eager loading of the customer relationship
        $query = Orders::where('store_id', $userId)
            ->with('customer') // Eager load the related customer
            ->orderBy('created_at', 'desc');

        // Apply keyword filter if provided
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            // Apply filtering safely
            $query->where(function ($q) use ($keyword) {
                $q->where('status', 'like', '%' . $keyword . '%')
                    ->orWhere('payment_method', 'like', '%' . $keyword . '%')
                    ->orWhere('shipping_address', 'like', '%' . $keyword . '%')
                    ->orWhere('total_amount', 'like', '%' . $keyword . '%')
                    ->orWhere('shipping_cost', 'like', '%' . $keyword . '%')
                    ->orWhereHas('customer', function ($subQuery) use ($keyword) {
                        $subQuery->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%')
                            ->orWhere('phone_number', 'like', '%' . $keyword . '%');
                    });
            });
        }

        // Paginate results
        $orders = $query->paginate($perPage);

        // Return response
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found'], 200);
        }

        return response()->json($orders);
    }





    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'required|integer',
            'store_id' => 'required|integer',
            'cart_id' => 'required|integer',
            'total_amount' => 'required|numeric|min:1',
            'shipping_address' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        $order = Orders::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
            'data' => $order
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Orders::findOrFail($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return $order;
    }

    public function updateStatus(Request $request, string $id)
    {
        $reorderRequest = Orders::findOrFail($id);

        if (!$reorderRequest) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $validatedData = $request->validate([
            'status' => 'string',
            'shipped_date' => 'nullable|date',
            'delivered_date' => 'nullable|date',
        ]);

        $reorderRequest->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully.',
            'data' => $reorderRequest
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Orders::findOrFail($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['success' => true, 'message' => 'Order deleted successfully.']);
    }
}
