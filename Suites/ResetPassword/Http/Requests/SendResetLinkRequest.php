<?php

namespace Modules\ResetPassword\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;

class SendResetLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Veuillez saisir votre adresse email.',
            'email.email'    => 'Veuillez saisir une adresse email valide.',
        ];
    }
}
