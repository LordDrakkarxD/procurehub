<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBiddingRequest extends FormRequest
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
        $id = $this->route('bidding')?->id;

        return [
            'number' => ['required', 'string', 'max:50', 'unique:biddings,number'.($id ? ','.$id : '')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,open,awarded,cancelled'],
            'opened_at' => ['nullable', 'date'],
            'awarded_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'O número do pregão é obrigatório.',
            'number.unique' => 'Já existe um pregão com este número.',
            'title.required' => 'O objeto/título é obrigatório.',
            'total_amount.required' => 'O valor é obrigatório.',
            'status.in' => 'Status inválido.',
        ];
    }
}
