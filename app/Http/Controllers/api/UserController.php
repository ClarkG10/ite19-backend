<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::all();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Retrieve the validated input data...
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return $user;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return User::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateUserAccount(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Retrieve the validated input data...
        $validated = $request->validated();

        $user->update($validated);

        return $user;
    }

    /**
     * Update the email of the specified resource in storage.
     */
    public function email(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Retrieve the validated input data...
        $validated = $request->validated();

        $user->email =  $validated['email'];

        $user->save();

        return $user;
    }

    /**
     * Update the password of the specified resource in storage.
     */
    public function password(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Retrieve the validated input data...
        $validated = $request->validated();

        $user->password = Hash::make($validated['password']);

        $user->save();

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return $user;
    }
}
