<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ClientForm extends Component
{
    public string $name = '';

    public string $email = '';

    public ?string $phone = null;

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $country_of_residence = null;

    public ?string $country_of_origin = null;

    public ?string $city_of_residence = null;

    public ?string $subscription_type = 'basic';

    public ?string $subscription_expires_at = null;

    public ?string $total_investment = null;

    public ?string $notes = null;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:8|confirmed',
            'country_of_residence' => 'nullable|string|max:255',
            'country_of_origin' => 'nullable|string|max:255',
            'city_of_residence' => 'nullable|string|max:255',
            'subscription_type' => 'nullable|string|in:basic,standard,premium',
            'subscription_expires_at' => 'nullable|date',
            'total_investment' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'role' => 'client',
            'is_active' => true,
        ]);

        Client::create([
            'user_id' => $user->id,
            'country_of_residence' => $this->country_of_residence,
            'country_of_origin' => $this->country_of_origin,
            'city_of_residence' => $this->city_of_residence,
            'subscription_type' => $this->subscription_type,
            'subscription_expires_at' => $this->subscription_expires_at ?: null,
            'total_investment' => $this->total_investment ?: 0,
            'notes' => $this->notes,
        ]);

        $this->reset([
            'name',
            'email',
            'phone',
            'password',
            'password_confirmation',
            'country_of_residence',
            'country_of_origin',
            'city_of_residence',
            'subscription_type',
            'subscription_expires_at',
            'total_investment',
            'notes',
        ]);

        session()->flash('success', 'Client créé avec succès.');

        return $this->redirect(route('admin.clients.index'));
    }

    public function render()
    {
        return view('livewire.client-form');
    }
}
