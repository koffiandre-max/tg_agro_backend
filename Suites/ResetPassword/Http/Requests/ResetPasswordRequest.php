<?php

namespace Modules\ResetPassword\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'                 => ['required', 'email'],
            'token'                 => ['required', 'string'],
            'new_password'          => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'                => 'Veuillez saisir votre adresse email.',
            'email.email'                   => 'Veuillez saisir une adresse email valide.',
            'token.required'                => 'Le lien de réinitialisation est invalide.',
            'new_password.required'         => 'Le nouveau mot de passe est obligatoire.',
            'new_password.min'              => 'Le mot de passe doit contenir au moins 8 caractères.',
            'new_password.confirmed'        => 'La confirmation ne correspond pas au nouveau mot de passe.',
        ];
    }
}

