<?php

namespace App\Livewire;

use Livewire\Component;

class ChoiceCard extends Component
{
    public string $name;

    public string $label;

    public $value;

    public ?string $precisionValue = null;

    public ?string $precisionModel = null;

    public ?string $placeholder = 'Précisez...';

    public function render()
    {
        return view('livewire.choice-card');
    }
}
