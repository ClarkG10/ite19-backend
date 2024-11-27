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

        // Retrieve the validated input data...
        $validated = $request->validated();

        $customer->update($validated);

        return $customer;
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateCustomerName(Request $request, string $id)
    {

        $customer = Customer::findOrFail($id);

        // Retrieve the validated input data...
        $validated = $request->validated();

        $customer->name =  $validated['name'];

        $customer->save();

        return $customer;
    }

    /**
     * Update the email of the specified resource in storage.
     */
    public function email(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        // Retrieve the validated input data...
        $validated = $request->validated();

        $customer->email =  $validated['email'];

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
