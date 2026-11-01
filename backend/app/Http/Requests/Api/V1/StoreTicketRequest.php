<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Mockery\Matcher\Any;

class StoreTicketRequest extends FormRequest
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
        return [
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'integer', Rule::exists('ticket_categories', 'id')->where('is_active', true)],
            'priority' => ['sometimes', 'string', Rule::in(['low', 'medium', 'high', 'urgent'])]
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'The selected category is inactive or does not exist'
        ];
    }
}
