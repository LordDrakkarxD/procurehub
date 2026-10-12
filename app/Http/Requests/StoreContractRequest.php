<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $id = $this->route('contract')?->id;

        return [
            'number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('contracts', 'number')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('contract')?->id),
            ],
            'bidding_id' => ['nullable', 'exists:biddings,id'],
            'company_id' => ['required', 'exists:companies,id'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'status' => ['required', 'in:active,closed,cancelled'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'O número do contrato é obrigatório.',
            'number.unique' => 'Já existe um contrato com este número.',
            'company_id.required' => 'A empresa é obrigatória.',
            'total_amount.required' => 'O valor do contrato é obrigatório.',
            'ends_on.after_or_equal' => 'A data final deve ser igual ou posterior à inicial.',
        ];
    }
}
