<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if (request()->routeIs('user.login')) {
            return [
                'email' => 'required|string|email|max:255',
                'password' => 'required|min:8',
            ];
        } else if (request()->routeIs('user.store')) {
            return [
                'email' => 'required|string|email|unique:App\Models\User',
                'password' => 'required|min:8|confirmed',
                'business_type' => 'required|string|max:255',
                'business_name' => 'required|string|max:255',
                'business_number' => 'required|string|max:255',
                'phone_number' => 'required|integer|min:10',
                'business_address' => 'required|string|max:255',
                'city' => 'required|string',
                'country' => 'required|string',
                'zipcode' => 'required|integer',
                'operating_hours' => 'required|string',
            ];
        } else if (request()->routeIs('user.email')) {
            return [
                'email' => 'required|string|email|max:255',
            ];
        } else if (request()->routeIs('user.password')) {
            return [
                'password' => 'required|confirmed|min:8',
            ];
        }
    }
}
