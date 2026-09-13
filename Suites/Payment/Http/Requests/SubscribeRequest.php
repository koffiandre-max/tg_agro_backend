<?php

namespace Modules\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'method' => ['required', 'in:card,mobile_money'],
            'auto_renew' => ['nullable', 'boolean'],
            'payer_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['required_if:method,mobile_money', 'nullable', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'method.required' => 'Choisissez un moyen de paiement.',
            'method.in' => 'Le moyen de paiement choisi est invalide.',
            'phone.required_if' => 'Le numéro de téléphone est requis pour le paiement Mobile Money.',
            'payer_name.max' => 'Le nom du payeur ne doit pas dépasser 120 caractères.',
        ];
    }
}
