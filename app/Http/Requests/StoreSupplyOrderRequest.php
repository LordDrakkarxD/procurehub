<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupplyOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contract_id' => ['required', 'exists:contracts,id'],
            'number' => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('supply_orders', 'number')->whereNull('deleted_at'),
            ],
            'ordered_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.contract_item_id' => ['required', 'exists:contract_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'O número da ordem é obrigatório.',
            'number.unique' => 'Já existe uma ordem com este número.',
            'items.required' => 'Adicione pelo menos um item.',
            'items.min' => 'Adicione pelo menos um item.',
        ];
    }
}
