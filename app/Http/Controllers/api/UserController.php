<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->query('keyword', null);

        $query = User::query();
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('product_name', 'like', "%{$keyword}%")
                    ->orWhere('product_type', 'like', "%{$keyword}%")
                    ->orWhere('brand', 'like', "%{$keyword}%");
            });
        }

        $users = $query->get();

        return response()->json($users);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        // Retrieve the validated input data...
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('images/store_images', 'public');
            $validated['image_path'] = $imagePath;
        }

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
