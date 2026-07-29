<?php

namespace App\Models;

use App\Contracts\SectionVisiteInterface;
use App\Models\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RapportVisite extends Model
{
    use HasStatus;

    protected $table = 'rapports_visite';

    protected $fillable = [
        'technicien_id',
        'date_visite',
        'client_id',
        'farm_id',
        'localisation_parcelle',
        'type_visite',
        'conditions_meteo',
        'superficie_visitee',
        'duree_visite',
        'latitude',
        'longitude',
        'statut',
        'date_envoi',
        'date_validation',
    ];

    protected function casts(): array
    {
        return [
            'date_visite' => 'date',
            'date_envoi' => 'datetime',
            'date_validation' => 'datetime',
            'superficie_visitee' => 'decimal:2',
        ];
    }

    public function technicien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function visiteCultures(): HasOne
    {
        return $this->hasOne(VisiteCulture::class, 'rapport_id');
    }

    public function visiteElevage(): HasOne
    {
        return $this->hasOne(VisiteElevage::class, 'rapport_id');
    }

    public function observationsFinales(): HasOne
    {
        return $this->hasOne(ObservationFinale::class, 'rapport_id');
    }

    public function prochaineVisite(): HasOne
    {
        return $this->hasOne(ProchaineVisite::class, 'rapport_id');
    }

    public function notesRapport(): HasOne
    {
        return $this->hasOne(NotesRapport::class, 'rapport_id');
    }

    /**
     * Relations polymorphiques : photos, validations, notifications
     */
    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    public function validations(): MorphMany
    {
        return $this->morphMany(Validation::class, 'validable');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    // Override HasStatus pour le statut en anglais (champ `statut` en français)
    protected function getSoumetableStatuses(): array
    {
        return ['Brouillon'];
    }

    protected function getValidableStatuses(): array
    {
        return ['En attente de validation'];
    }

    protected function getValideStatus(): string
    {
        return 'Validé';
    }

    protected function getRejeteStatus(): string
    {
        return 'Rejeté';
    }
}