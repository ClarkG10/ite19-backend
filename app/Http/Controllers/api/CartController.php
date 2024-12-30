<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Inventory;

class CartController extends Controller
{
    public function getCart(Request $request)
    {
        $userId = $request->user()->customer_id;

        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Retrieve the cart for the user, including only items where is_ordered is false
        $cart = Cart::where('customer_id', $userId)
            ->where('is_ordered', false)
            ->with('items')
            ->get();

        // Check if the cart exists
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found'
            ], 404);
        }

        return response()->json($cart, 200);
    }

    public function cartStoreIndex(Request $request)
    {
        $userId = $request->user()->id;

        $cart = Cart::where('store_id', $userId)
            ->with('items')
            ->get();

        // Check if the cart exists
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found'
            ], 404);
        }

        return response()->json($cart, 200);
    }

    public function cartShow($id)
    {
        return Cart::with('items')->findOrFail($id);
    }


    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:App\Models\User,id',
            'product_id' => 'required|exists:App\Models\Product,product_id',
            'customer_id' => 'required|exists:App\Models\Customer,customer_id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Check if there's an existing cart for the customer and store
        $cart = Cart::where('customer_id', $validated['customer_id'])
            ->where('store_id', $validated['store_id'])
            ->where('is_ordered', false)
            ->first();

        // If no active cart exists or the existing cart is delivered, create a new one
        if (!$cart) {
            $cart = Cart::create([
                'customer_id' => $validated['customer_id'],
                'store_id' => $validated['store_id'],
                'is_ordered' => false,
            ]);
        }

        // Check inventory for the product
        $inventory = Inventory::where('store_id', $validated['store_id'])
            ->where('product_id', $validated['product_id'])
            ->first();

        if (!$inventory || $inventory->quantity < $validated['quantity']) {
            return response()->json(['success' => false, 'message' => 'Insufficient stock'], 400);
        }

        // Check if the item already exists in the cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            // Increment the quantity if the item exists
            $newQuantity = $cartItem->quantity + $validated['quantity'];

            // Check inventory for the updated quantity
            if ($inventory->quantity < $newQuantity) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for the updated quantity'], 400);
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Add a new item to the cart
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'price' => $inventory->new_price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'data' => $cartItem,
        ]);
    }


    public function updateQuantity($action, $itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);

        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        if ($action === 'add') {
            $cartItem->quantity += 1;
        } else if ($action === 'subtract') {
            if ($cartItem->quantity > 1) { // Prevent quantity from going below 1
                $cartItem->quantity -= 1;
            } else {
                return response()->json(['error' => 'Quantity cannot be less than 1'], 400);
            }
        } else {
            return response()->json(['error' => 'Invalid action'], 400);
        }

        $cartItem->save();

        return response()->json($cartItem);
    }

    public function updateCartStatus($id)
    {
        $cart = Cart::findOrFail($id);

        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], 404);
        }

        $cart->is_ordered = true;
        $cart->save();

        return response()->json($cart);
    }

    public function removeItem($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Item removed']);
    }
}
