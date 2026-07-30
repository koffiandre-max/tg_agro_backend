<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotesRapport extends Model
{
    protected $table = 'notes_rapport';

    protected $fillable = [
        'rapport_id',
        'note_interne',
        'message_client',
    ];

    public function rapport(): BelongsTo
    {
        return $this->belongsTo(RapportVisite::class, 'rapport_id');
    }
}
