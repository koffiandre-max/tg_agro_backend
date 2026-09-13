<?php

namespace Modules\SendEmail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_email' => ['required', 'email', 'max:255'],
            'to_name' => ['nullable', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_html' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_email.required' => 'L\'adresse du destinataire est obligatoire.',
            'to_email.email' => 'L\'adresse du destinataire est invalide.',
            'subject.required' => 'Le sujet est obligatoire.',
            'body.required' => 'Le contenu de l\'email est obligatoire.',
        ];
    }
}
