<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:10240'], // 10MB
            'attachable_type' => ['required', 'string'],
            'attachable_id' => ['nullable', 'integer'],
            'token' => ['nullable', 'uuid'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        // Example: Adding an "after" validation hook
        $validator->after(function ($validator) {
            if (!$this->attachable_id && !$this->token) {
                $validator->errors()->add('token', 'Informe o registro ou o token temporário.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Selecione um arquivo.',
            'file.file' => 'O envio precisa ser um arquivo válido.',
            'file.max' => 'O arquivo não pode ultrapassar 10MB.',
            'attachable_type.required' => 'O tipo do registro é obrigatório.',
        ];
    }
}
