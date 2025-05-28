<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class CharacterRequest extends FormRequest
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
        if($route->getName() == 'characters.get.by.level'){
            return [
                'hskLevel'  => ['required', 'integer', Rule::in(range(1,6))],
            ];
        } elseif($route->getName() == 'characters.get.by.search'){
            return [
                'keyword'  => ['required', 'string', 'min:1', 'max:8'],
            ];
        } elseif($route->getName() == 'characters.get.details'){
            return [
                'characterId'  => ['required', 'integer', 'exists:characters,id'],
            ];
        } elseif($route->getName() == 'characters.get.id'){
            return [
                'character'  => ['required', 'string'],
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
        if (isset($this->hskLevel)){
            $this->merge([
                'hskLevel' => $this->hskLevel,
            ]);
        }
        if (isset($this->keyword)){
            $this->merge([
                'keyword' => $this->keyword,
            ]);
        }
        if (isset($this->characterId)){
            $this->merge([
                'characterId' => $this->characterId,
            ]);
        }
        if (isset($this->character)){
            $this->merge([
                'character' => $this->character,
            ]);
        }
    }
}