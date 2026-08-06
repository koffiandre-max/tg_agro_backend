<?php

namespace App\Models;

use App\Enums\ConditionMeteo;
use App\Enums\DureeVisite;
use App\Enums\StatutRapport;
use App\Enums\TypeActivite;
use App\Enums\TypeVisite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RapportVisite extends Model
{
    protected $table = 'rapport_visites';

    protected $fillable = [
        'technicien_id',
        'date_visite',
        'client_id',
        'farm_id',
        'localisation_parcelle',
        'type_visite',
        'type_activite',
        'conditions_meteo',
        'superficie_visitee_ha',
        'duree_visite',
        'gps_latitude',
        'gps_longitude',
        'resume_visite',
        'niveau_alerte',
        'description_alerte',
        'prochaine_visite_date',
        'prochaine_visite_raison',
        'note_interne',
        'message_client',
        'statut',
        'motif_rejet',
        'valide_par',
        'valide_at',
        'envoye_at',
    ];

    protected function casts(): array
    {
        return [
            'date_visite' => 'date',
            'superficie_visitee_ha' => 'decimal:2',
            'gps_latitude' => 'decimal:7',
            'gps_longitude' => 'decimal:7',
            'prochaine_visite_date' => 'date',
            'valide_at' => 'datetime',
            'envoye_at' => 'datetime',
            'type_visite' => TypeVisite::class,
            'type_activite' => TypeActivite::class,
            'conditions_meteo' => ConditionMeteo::class,
            'duree_visite' => DureeVisite::class,
            'statut' => StatutRapport::class,
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

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(RapportVisitePhoto::class, 'rapport_visite_id');
    }

    public function visiteCultures(): HasMany
    {
        return $this->hasMany(VisiteCulture::class, 'rapport_id');
    }

    public function visiteElevage(): HasMany
    {
        return $this->hasMany(VisiteElevage::class, 'rapport_id');
    }

    public function visiteAutres(): HasMany
    {
        return $this->hasMany(VisiteAutre::class, 'rapport_id');
    }

    public function observationsFinales(): HasMany
    {
        return $this->hasMany(ObservationFinale::class, 'rapport_id');
    }

    public function prochaineVisite(): HasMany
    {
        return $this->hasMany(ProchaineVisite::class, 'rapport_id');
    }

    public function notesRapport(): HasMany
    {
        return $this->hasMany(NotesRapport::class, 'rapport_id');
    }

    public function validations()
    {
        return $this->morphMany(Validation::class, 'validable');
    }
}
