<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(UserRequest $request)
    {
        $user = $this->authenticate($request->email, $request->password);

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $this->createTokenResponse($user['model'], $user['type']);
    }

    protected function authenticate($email, $password)
    {
        // Check User table
        $user = User::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            return ['model' => $user, 'type' => 'user'];
        }

        // Check Customer table
        $customer = Customer::where('email', $email)->first();
        if ($customer && Hash::check($password, $customer->password)) {
            return ['model' => $customer, 'type' => 'customer'];
        }

        return null;
    }

    protected function createTokenResponse($model, $type)
    {
        return response()->json([
            'user'  => $model,
            'token' => $model->createToken($model->email)->plainTextToken,
            'type'  => $type,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }
}
