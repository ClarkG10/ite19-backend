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

        // Base query
        $query = Orders::where('store_id', $userId)
            ->orderBy('created_at', 'desc');

        // Apply keyword filter if provided
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('quantity', 'like',  $keyword)
                    ->orWhere('status', 'like', '%' . $keyword . '%')
                    ->orWhere('payment_method', 'like', '%' . $keyword . '%')
                    ->orWhere('shipping_address', 'like', '%' . $keyword . '%')
                    ->orWhere('total_amount', 'like', $keyword)
                    ->orWhere('price', 'like',  $keyword);
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

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $cartItems = CartItem::where('cart_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $totalAmount = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $order = Orders::create([
            'store_id' => $validated['store_id'],
            'customer_id' => auth()->id(),
            'shipping_address' => $validated['shipping_address'],
            'payment_method' => $validated['payment_method'],
            'total_amount' => $totalAmount,
            'status' => 'Pending',
        ]);

        foreach ($cartItems as $item) {
            $inventory = Inventory::where('store_id', $validated['store_id'])
                ->where('product_id', $item->product_id)
                ->first();

            if ($inventory && $inventory->quantity >= $item->quantity) {
                $inventory->quantity -= $item->quantity;
                $inventory->save();

                $order->cartItems()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);

                $item->delete();
            } else {
                return response()->json(['error' => 'Stock issue with product ID ' . $item->product_id], 400);
            }
        }

        return response()->json(['message' => 'Order placed successfully', 'order' => $order]);
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
            'payment_method' => 'required|string',
            'status' => 'required|string',
            'shipping_address' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
            'shipped_date' => 'required|date',
            'delivered_date' => 'required|date',
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
