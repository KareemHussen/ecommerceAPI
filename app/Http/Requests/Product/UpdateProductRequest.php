<?php

namespace App\Http\Requests\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'string|max:255',
            'description' => 'string|max:255',
            'quantity' => 'integer|min:1',
            'live' => 'boolean',
            'price' => 'required_with:priceBefore|numeric|min:1|max:99998.99|numeric|decimal:0,2',
            'priceBefore' => 'required_with:price|numeric|min:1|max:99999.99|decimal:0,2|gte:price',
            'image' => 'image|max:1024',
            'category_id' => 'integer|min:1|exists:categories,id',
        ];
    }
}
