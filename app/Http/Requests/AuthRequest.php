<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
        $route = Route::current();
        if($route->getName() == 'user.register'){
            return [
                'fname'      => ['required', 'string', 'min:2', 'max:30'],
                'lname'      => ['required', 'string', 'min:2', 'max:30'],
                'email'      => ['required', 'email', 'unique:users,email'],
                'password'   => ['required', 'string', 'min:8', 'max:30', 'confirmed'],
                'phone'      => ['required', 'string', 'regex:/^\+\d{10,15}$/'],
                'age'        => ['required', 'string'],
                'country_id' => ['required', 'integer', 'exists:countries,id'],
                'level_id'   => ['required', 'integer', 'exists:levels,id'],
            ];
        } elseif($route->getName() == 'user.login'){
            return [
                'email'    => ['required', 'email'],
                'password' => ['required', 'string', 'min:8'],
            ];
        }
    }

    public function prepareForValidation()
    {
        if (isset($this->categoryId)){
            $this->merge([
                'categoryId' => $this->categoryId,
            ]);
        }
    }
}
