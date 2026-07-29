<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class NotesRapportData extends Data
{
    public function __construct(
        #[Nullable]
        public ?string $note_interne = null,

        #[Nullable]
        public ?string $message_client = null,
    ) {}
}
