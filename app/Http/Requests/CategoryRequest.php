<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        if($route->getName() == 'category.store'){
            return [
                'name'       => ['required', 'string', 'min:2', 'max:30', 'unique:categories'],
                'parent_id'  => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            ];
        } elseif(in_array($route->getName(), ['category.get.cards', 'category.get.quiz.characters'])){
            return [
                'categoryId'  => ['required', 'integer', 'exists:categories,id'],
            ];
        } elseif($route->getName() == 'category.update.name'){
            return [
                'name'       => ['required', 'string', 'min:2', 'max:30', Rule::unique('categories', 'name')->ignore($this->categoryId)],
                'categoryId' => ['required', 'integer', 'exists:categories,id'],
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
