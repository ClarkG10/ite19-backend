<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Customer::all();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the input data
        $validatedData = $request->validate([
            'email' => 'required|string|email|unique:App\Models\User|unique:App\Models\Customer',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|string',
            'address' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'is_frequent_shopper' => 'nullable|boolean',
            'role' => 'required|string'
        ]);

        // Hash the password
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Handle file upload if image_path is provided
        if ($request->hasFile('image_path')) {
            $validatedData['image_path'] = $request->file('image_path')->store('images/customer_images', 'public');
        }

        // Create the customer
        $customer = Customer::create($validatedData);

        // Return a response without sensitive data
        return response()->json([
            'id' => $customer->id,
            'email' => $customer->email,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'phone_number' => $customer->phone_number,
            'address' => $customer->address,
            'image_path' => $customer->image_path,
            'role' . $customer->role,
            'created_at' => $customer->created_at,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Customer::FindOrFail($id);
    }

    public function updateCustomerAccount(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $validatedData = $request->validate([
            'email' => 'required|string|email|unique:App\Models\Customer',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|integer',
            'address' => 'required|string',
            'image_path' =>  'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        $customer->update($validatedData);

        return $customer;
    }

    public function updateDeliveryDetails(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $validatedData = $request->validate([
            'phone_number' => 'required|integer',
            'address' => 'required|string',
        ]);

        $customer->update($validatedData);

        return $customer;
    }


    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the email of the specified resource in storage.
     */
    public function isFrequentShopper(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        // Retrieve the validated input data...
        $validated = $request->validated();

        $customer->is_frequent_shopper =  $validated['is_frequent_shopper'];

        $customer->save();

        return $customer;
    }

    /**
     * Update the password of the specified resource in storage.
     */
    public function password(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        // Retrieve the validated input data...
        $validated = $request->validated();

        $customer->password = Hash::make($validated['password']);

        $customer->save();

        return $customer;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);

        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully.'], 200);
    }
}
