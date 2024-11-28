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
        // Retrieve the validated input data...
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        $customer = Customer::create($validated);

        return $customer;
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
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        $validatedData = $request->validate([
            'email' => 'required|string|email|unique:App\Models\Customer',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
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

        $customer->email =  $validated['is_frequent_shopper'];

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
