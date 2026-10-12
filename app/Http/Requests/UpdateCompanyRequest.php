<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->document) {
            $this->merge([
                'document' => preg_replace('/\D/', '', $this->document),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:20', 'unique:companies,document,'.$this->route('company')->id],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da empresa é obrigatório.',
            'document.required' => 'O CNPJ é obrigatório.',
            'document.unique' => 'Este CNPJ já está cadastrado.',
            'email.email' => 'Informe um e-mail válido.',
        ];
    }
}
