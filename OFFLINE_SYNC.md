# Synchronisation hors-ligne mobile (TG Agro)

Ce document décrit le fonctionnement **offline-first** de l'API mobile :
le technicien peut consulter et saisir des données sans connexion, puis
resynchroniser quand le réseau est rétabli.

## Principe général

Le mobile garde une copie locale (SQLite) des données du technicien :

- **Lecture hors-ligne** : un instantané (`bootstrap`) puis des changements
  incrémentaux (`changelog`) permettent d'alimenter le cache local.
- **Écriture hors-ligne** : chaque action (créer une saisie, changer un statut…)
  est mise dans une **file locale (outbox)**. Dès que le réseau revient,
  le mobile envoie le lot via `/mobile/sync`.

## 1. Lecture (téléchargement des données)

| Endpoint | Rôle |
|---|---|
| `GET /api/v1/mobile/bootstrap` | Premier lancement : instantané complet + `current_cursor` |
| `GET /api/v1/mobile/changelog?cursor=&entity_type=&limit=` | Changements incrémentaux après le curseur (en-tête `X-Next-Cursor`) |
| `POST /api/v1/mobile/changelog/ack` `{cursor}` | Acquitte les changements reçus |

Le journal (`changelogs`) contient les opérations `created`, `updated`,
`deleted` (tombstones) — les suppressions sont donc bien propagées au mobile.

## 2. Écriture (remontée des opérations hors-ligne)

`POST /api/v1/mobile/sync` — envoie la file locale d'opérations :

```json
{
  "device_id": "iphone-7f3a",
  "operations": [
    {
      "client_uuid": "uuid-local-généré-une-fois",
      "entity_type": "DataEntry",
      "operation": "created",
      "entity_id": null,
      "payload": { "farm_id": 1, "client_id": 2, "observations": "…" },
      "client_updated_at": "2026-09-08T09:00:00Z"
    },
    {
      "client_uuid": "uuid-local-2",
      "entity_type": "Mission",
      "operation": "updated",
      "entity_id": 3,
      "payload": { "status": "completed" }
    }
  ]
}
```

Réponse par opération (`results`) :

| Statut | Signification |
|---|---|
| `applied` | Appliquée ; `entity_id` = ID serveur à mémoriser |
| `idempotent` | Déjà appliquée (renvoi) ; renvoie l'ID serveur existant |
| `skipped` | Conflit : la version serveur est plus récente (Last-Write-Wins) |
| `error` | Rejetée (validation, autorisation, fichier requis…) |

### Idempotence — le point clé
Chaque opération porte un **`client_uuid`** généré **une seule fois** par le
mobile (persisté en local). Si le mobile retente l'envoi (perte réseau,
redémarrage), le serveur reconnaît le `client_uuid` et **ne crée pas de
doublon** : il répond `idempotent` avec l'ID serveur déjà créé.

### Résolution de conflits (Last-Write-Wins)
Chaque entité porte un `client_updated_at` (date de la dernière modification
sur l'appareil). Lors d'un `updated`, si la version serveur est plus récente,
l'opération est ignorée (`skipped`) et la version serveur est conservée.

## 3. Fichiers (rapports PDF, photos)

Les fichiers **ne passent pas par l'outbox** (pas de binaire en JSON). Ils sont
téléversés via leurs endpoints multipart une fois connecté, toujours avec un
`client_uuid` pour éviter les doublons :

- `POST /api/v1/technician/reports` (multipart) — champ `client_uuid`
- `POST /api/v1/technician/photos` (multipart) — champ `client_uuid`

## 4. Cycle de synchronisation conseillé (côté mobile)

1. **En ligne** : `POST /mobile/sync` avec toute la file locale.
2. Mémoriser le mapping `client_uuid → entity_id` renvoyé.
3. Recharger `GET /mobile/changelog?cursor=<dernier curseur>` pour récupérer
   les changements serveur (y compris les entités créées à l'étape 1, et les
   validations/admin).
4. `POST /mobile/changelog/ack` avec le dernier curseur lu.
5. Vider de la file locale les opérations `applied`/`idempotent`/`skipped`
   (garder les `error` pour correction).

## 5. Tables ajoutées

- `changelogs` : journal des changements (lecture mobile).
- `sync_operations` : mapping `client_uuid → entité serveur` (idempotence).
- `data_entries.client_updated_at`, `reports.client_updated_at`,
  `photos.client_updated_at` : horodatage de modification côté appareil (LWW).