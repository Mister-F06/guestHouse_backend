<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
        return [
            'firstname' => 'required|string',
            'lastname'  => 'required|string',
            'email'     => 'required|email|'. Rule::unique('users' , 'email'),
            'telephone' => 'required|string|'. Rule::unique('users' , 'telephone'),
            'password'  => ['required','string' , 'min:8' , 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&*()_+[\]{}|;:,.<>?])[A-Za-z\d@#$%^&*()_+[\]{}|;:,.<>?]{8,}$/' , 'confirmed'],
            'accept_terms' => 'required|boolean'
        ];
    }
}
