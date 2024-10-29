<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FoodRequest extends FormRequest
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
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'preparation_time' => 'nullable|integer|min:0',
            'image_path' => 'nullable|string|max:255',
            'is_vegetarian' => 'boolean',
            'is_spicy' => 'boolean',
            'allergens' => 'nullable|array',
            'is_available' => 'boolean',
            'sort_order' => 'integer|min:0'
        ];
    }
}
