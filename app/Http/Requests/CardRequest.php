<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class CardRequest extends FormRequest
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
        if($route->getName() == 'card.store'){
            return [
                'character_id'  => ['required', 'integer', 'exists:characters,id'],
                'category_id'   => ['required', 'integer', 'exists:categories,id'],
            ];
        } elseif(in_array($route->getName(), ['card.show', 'card.store.description', 'card.destroy'])){
            return [
                'cardId'  => ['required', 'integer', 'exists:cards,id'],
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
        if (isset($this->cardId)){
            $this->merge([
                'cardId' => $this->cardId,
            ]);
        }
    }
}
