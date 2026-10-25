<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('ticket_category')?->id ?? $this->route('ticket_category');

        return [
            'name' => [$this->isMethod('post') ? 'required' : 'sometimes', 'string', 'max:255', Rule::unique('ticket_categories', 'name')->ignore($categoryId)],
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
