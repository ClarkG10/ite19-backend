<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\FrequentShopper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FrequentShopperController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return FrequentShopper::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function toggleFrequentShopper(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'store_id' => 'required|exists:users,id',
            'frequent_shopper' => 'required|boolean', // True or False
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Get the data from the request
        $customerId = $request->customer_id;
        $storeId = $request->store_id;
        $frequentShopperStatus = $request->frequent_shopper;

        // Check if the record exists for the customer and store
        $frequentShopper = FrequentShopper::where('customer_id', $customerId)
            ->where('store_id', $storeId)
            ->first();

        if ($frequentShopper) {
            // If record exists, update the frequent shopper status
            $frequentShopper->frequent_shopper = $frequentShopperStatus;
            $frequentShopper->save();
        } else {
            // If no record exists, create a new one
            FrequentShopper::create([
                'customer_id' => $customerId,
                'store_id' => $storeId,
                'frequent_shopper' => $frequentShopperStatus,
            ]);
        }

        return response()->json([
            'message' => 'Frequent shopper status updated successfully.',
            'frequent_shopper' => $frequentShopperStatus
        ]);
    }
}
