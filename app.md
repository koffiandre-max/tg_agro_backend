# TG'INVEST — Analyse technique du CDC

## Architecture générale

Application Laravel 13 avec architecture modulaire.
Trois profils utilisateurs : client diaspora, technicien terrain, admin.

---

## 1. Tables / Migrations

### Table `users` (existante — à enrichir)

| Champ               | Type                                | Contrainte       |
| ------------------- | ----------------------------------- | ---------------- |
| `id`                | bigIncrements                       | PK               |
| `name`              | string(255)                         |                  |
| `email`             | string(255)                         | unique           |
| `password`          | string(255)                         | bcrypt           |
| `role`              | enum('client','technician','admin') | default 'client' |
| `phone`             | string(20)                          | nullable         |
| `avatar`            | string(255)                         | nullable         |
| `is_active`         | boolean                             | default true     |
| `email_verified_at` | timestamp                           | nullable         |
| `remember_token`    | string(100)                         | nullable         |
| `timestamps`        |                                     |                  |

### Table `clients` — Informations spécifiques client diaspora

| Champ                     | Type                               | Contrainte            |
| ------------------------- | ---------------------------------- | --------------------- |
| `id`                      | bigIncrements                      | PK                    |
| `user_id`                 | unsignedBigInteger                 | FK → users.id, unique |
| `country_of_residence`    | string(100)                        | ex: France            |
| `country_of_origin`       | string(100)                        | ex: Côte d'Ivoire     |
| `city_of_residence`       | string(100)                        |                       |
| `id_document_type`        | string(50)                         | nullable              |
| `id_document_number`      | string(100)                        | nullable              |
| `subscription_type`       | enum('basic','standard','premium') | default 'basic'       |
| `subscription_expires_at` | date                               | nullable              |
| `total_investment`        | decimal(15,2)                      | default 0             |
| `notes`                   | text                               | nullable              |
| `timestamps`              |                                    |                       |

### Table `technicians` — Informations spécifiques technicien terrain

| Champ                     | Type               | Contrainte                 |
| ------------------------- | ------------------ | -------------------------- |
| `id`                      | bigIncrements      | PK                         |
| `user_id`                 | unsignedBigInteger | FK → users.id, unique      |
| `phone_secondary`         | string(20)         | nullable                   |
| `location_base`           | string(255)        | ville/région de base en CI |
| `is_available`            | boolean            | default true               |
| `max_concurrent_missions` | integer            | default 5                  |
| `current_workload`        | integer            | default 0                  |
| `notes`                   | text               | nullable                   |
| `timestamps`              |                    |                            |

### Table `farms` — Exploitations agricoles

| Champ                    | Type                               | Contrainte                                                   |
| ------------------------ | ---------------------------------- | ------------------------------------------------------------ |
| `id`                     | bigIncrements                      | PK                                                           |
| `client_id`              | unsignedBigInteger                 | FK → clients.id                                              |
| `name`                   | string(255)                        | nom de l'exploitation                                        |
| `location`               | string(255)                        | localisation textuelle                                       |
| `latitude`               | decimal(10,7)                      | nullable                                                     |
| `longitude`              | decimal(10,7)                      | nullable                                                     |
| `total_area_hectares`    | decimal(10,2)                      |                                                              |
| `culture_type`           | string(100)                        | type de culture principale                                   |
| `status`                 | enum('active','inactive','fallow') | default 'active'                                             |
| `expected_harvest_date`  | date                               | nullable                                                     |
| `crop_stage`             | string(100)                        | nullable (semis, croissance, floraison, maturation, récolte) |
| `crop_stage_progress`    | integer                            | default 0 (0-100%)                                           |
| `last_visit_date`        | date                               | nullable                                                     |
| `assigned_technician_id` | unsignedBigInteger                 | nullable, FK → technicians.id                                |
| `notes`                  | text                               | nullable                                                     |
| `timestamps`             |                                    |                                                              |

### Table `missions` — Missions terrain

| Champ            | Type                                                  | Contrainte          |
| ---------------- | ----------------------------------------------------- | ------------------- |
| `id`             | bigIncrements                                         | PK                  |
| `technician_id`  | unsignedBigInteger                                    | FK → technicians.id |
| `farm_id`        | unsignedBigInteger                                    | FK → farms.id       |
| `title`          | string(255)                                           |                     |
| `description`    | text                                                  | nullable            |
| `scheduled_date` | date                                                  |                     |
| `completed_at`   | datetime                                              | nullable            |
| `status`         | enum('pending','in_progress','completed','cancelled') | default 'pending'   |
| `notes`          | text                                                  | nullable            |
| `timestamps`     |                                                       |                     |

### Table `reports` — Rapports PDF

| Champ                | Type                                              | Contrainte                      |
| -------------------- | ------------------------------------------------- | ------------------------------- |
| `id`                 | bigIncrements                                     | PK                              |
| `farm_id`            | unsignedBigInteger                                | FK → farms.id                   |
| `client_id`          | unsignedBigInteger                                | FK → clients.id                 |
| `technician_id`      | unsignedBigInteger                                | FK → technicians.id (nullable)  |
| `title`              | string(255)                                       |                                 |
| `type`               | enum('monthly','soil_analysis','harvest','other') |                                 |
| `file_path`          | string(255)                                       | chemin du PDF                   |
| `file_original_name` | string(255)                                       |                                 |
| `file_size`          | integer                                           | en bytes                        |
| `notes`              | text                                              | nullable                        |
| `status`             | enum('pending','validated','rejected')            | default 'pending'               |
| `rejection_reason`   | text                                              | nullable                        |
| `validated_by`       | unsignedBigInteger                                | nullable, FK → users.id (admin) |
| `validated_at`       | datetime                                          | nullable                        |
| `seen_by_client`     | boolean                                           | default false (badge "Nouveau") |
| `timestamps`         |                                                   |                                 |

### Table `photos` — Photos géolocalisées

| Champ                  | Type               | Contrainte                   |
| ---------------------- | ------------------ | ---------------------------- |
| `id`                   | bigIncrements      | PK                           |
| `farm_id`              | unsignedBigInteger | FK → farms.id                |
| `client_id`            | unsignedBigInteger | FK → clients.id              |
| `technician_id`        | unsignedBigInteger | FK → technicians.id          |
| `photo_path`           | string(255)        |                              |
| `thumbnail_path`       | string(255)        | nullable                     |
| `caption`              | string(255)        | nullable                     |
| `latitude`             | decimal(10,7)      | nullable (GPS auto)          |
| `longitude`            | decimal(10,7)      | nullable (GPS auto)          |
| `taken_at`             | datetime           | date/heure de la prise       |
| `is_visible_to_client` | boolean            | default false (admin décide) |
| `file_size`            | integer            | nullable                     |
| `timestamps`           |                    |                              |

### Table `data_entries` — Saisies données agronomiques

| Champ                    | Type                                   | Contrainte                 |
| ------------------------ | -------------------------------------- | -------------------------- |
| `id`                     | bigIncrements                          | PK                         |
| `farm_id`                | unsignedBigInteger                     | FK → farms.id              |
| `client_id`              | unsignedBigInteger                     | FK → clients.id            |
| `technician_id`          | unsignedBigInteger                     | FK → technicians.id        |
| `crop_stage`             | string(100)                            |                            |
| `crop_stage_progress`    | integer                                | 0-100                      |
| `estimated_harvest_date` | date                                   | nullable                   |
| `inputs_used`            | text                                   | nullable (JSON d'intrants) |
| `observations`           | text                                   | nullable                   |
| `weather_conditions`     | string(100)                            | nullable                   |
| `status`                 | enum('pending','validated','rejected') | default 'pending'          |
| `validated_by`           | unsignedBigInteger                     | nullable, FK → users.id    |
| `validated_at`           | datetime                               | nullable                   |
| `timestamps`             |                                        |                            |

### Table `market_prices` — Prix du marché local

| Champ            | Type          | Contrainte     |
| ---------------- | ------------- | -------------- |
| `id`             | bigIncrements | PK             |
| `product_name`   | string(100)   |                |
| `unit`           | string(50)    | kg, tonne, sac |
| `price_per_unit` | decimal(15,2) |                |
| `currency`       | string(10)    | default 'FCFA' |
| `region`         | string(100)   | nullable       |
| `recorded_at`    | date          |                |
| `source`         | string(255)   | nullable       |
| `timestamps`     |               |                |

### Table `messages` — Messagerie interne

| Champ         | Type               | Contrainte          |
| ------------- | ------------------ | ------------------- |
| `id`          | bigIncrements      | PK                  |
| `sender_id`   | unsignedBigInteger | FK → users.id       |
| `receiver_id` | unsignedBigInteger | FK → users.id       |
| `subject`     | string(255)        | nullable            |
| `message`     | text               |                     |
| `is_read`     | boolean            | default false       |
| `read_at`     | datetime           | nullable            |
| `parent_id`   | unsignedBigInteger | nullable (reply to) |
| `timestamps`  |                    |                     |

### Table `subscriptions` — Abonnements

| Champ               | Type                                           | Contrainte        |
| ------------------- | ---------------------------------------------- | ----------------- |
| `id`                | bigIncrements                                  | PK                |
| `client_id`         | unsignedBigInteger                             | FK → clients.id   |
| `type`              | enum('basic','standard','premium')             |                   |
| `amount`            | decimal(15,2)                                  |                   |
| `currency`          | string(10)                                     | default 'EUR'     |
| `start_date`        | date                                           |                   |
| `end_date`          | date                                           |                   |
| `status`            | enum('active','expired','cancelled','pending') | default 'pending' |
| `payment_method`    | string(50)                                     | nullable          |
| `payment_reference` | string(255)                                    | nullable          |
| `auto_renew`        | boolean                                        | default true      |
| `timestamps`        |                                                |                   |

---

## 2. Laravel Data (DTOs)

Le projet utilise **Laravel Data** (https://spatie.be/docs/laravel-data) pour gérer les Data Transfer Objects et valider les données d'entrée.

### Installation

```bash
composer require spatie/laravel-data
```

### Structure des Data Objects

```php
// App/Data/ClientData.php
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Data\PaginatedDataList;
use Spatie\LaravelData\Attributes\Validation\Required;

class ClientData extends Data
{
    #[Required]
    public string $name;

    #[Required]
    public string $email;

    #[Required]
    public string $phone;

    public ?string $country_of_residence;
    public ?string $country_of_origin;
    public ?string $city_of_residence;
    public ?string $id_document_type;
    public ?string $id_document_number;
    public string $subscription_type = 'basic';
}

// App/Data/FarmData.php
class FarmData extends Data
{
    #[Required]
    public string $name;

    #[Required]
    public int $client_id;

    #[Required]
    public string $location;

    public ?float $latitude;
    public ?float $longitude;

    #[Required]
    public float $total_area_hectares;

    public ?string $culture_type;
    public string $status = 'active';
    public ?string $expected_harvest_date;
    public ?string $crop_stage;
    public int $crop_stage_progress = 0;
}

// App/Data/MissionData.php
class MissionData extends Data
{
    #[Required]
    public string $title;

    #[Required]
    public int $technician_id;

    #[Required]
    public int $farm_id;

    public ?string $description;
    public ?string $scheduled_date;
    public string $status = 'pending';
}

// App/Data/ReportData.php
class ReportData extends Data
{
    #[Required]
    public string $title;

    #[Required]
    public int $farm_id;

    #[Required]
    public int $client_id;

    public ?int $technician_id;

    #[Required]
    public string $type;

    public ?string $file_path;
    public ?string $file_original_name;
    public ?int $file_size;
    public ?string $notes;
    public string $status = 'pending';
}

// App/Data/PhotoData.php
class PhotoData extends Data
{
    #[Required]
    public int $farm_id;

    #[Required]
    public int $client_id;

    public ?int $technician_id;

    #[Required]
    public string $photo_path;

    public ?string $thumbnail_path;
    public ?string $caption;
    public ?float $latitude;
    public ?float $longitude;
    public ?string $taken_at;
    public bool $is_visible_to_client = false;
}

// App/Data/DataEntryData.php
class DataEntryData extends Data
{
    #[Required]
    public int $farm_id;

    #[Required]
    public int $client_id;

    #[Required]
    public int $technician_id;

    public ?string $crop_stage;
    public ?int $crop_stage_progress;
    public ?string $estimated_harvest_date;
    public ?string $inputs_used;
    public ?string $observations;
    public ?string $weather_conditions;
    public string $status = 'pending';
}

// App/Data/SubscriptionData.php
class SubscriptionData extends Data
{
    #[Required]
    public int $client_id;

    #[Required]
    public string $type;

    #[Required]
    public float $amount;

    public string $currency = 'EUR';

    #[Required]
    public string $start_date;

    #[Required]
    public string $end_date;

    public string $status = 'pending';
    public ?string $payment_method;
    public ?string $payment_reference;
    public bool $auto_renew = true;
}
```

### Utilisation dans les Contrôleurs

```php
// Exemple d'utilisation dans un contrôleur
use App\Data\FarmData;
use Illuminate\Http\Request;

public function store(Request $request)
{
    // Validation automatique via Laravel Data
    $validated = FarmData::from($request->all());

    // Création du modèle
    Farm::create($validated->toArray());

    return redirect()->route('admin.farms.index');
}

// Pour les listes paginées
public function index()
{
    $farms = FarmData::from(Farm::paginate(10));

    return view('admin.farms.index', compact('farms'));
}
```

### Avantages

- **Validation automatique** des données d'entrée
- **Typage fort** avec les DTOs
- **Transformation** des données entre couches
- **Documentation** du code via les Data Objects
- **Réutilisabilité** des objets de données

---

## 3. Modèles Eloquent

```php
// App\Models\User.php (extends Authenticatable)
// Rôles: client | technician | admin
// Relations: clientProfile(), technicianProfile(), sentMessages(), receivedMessages()

// Modules/Portail/Models/Client.php
// Relations: user(), farms(), reports(), photos(), subscriptions()

// Modules/Technitian/Models/Technician.php
// Relations: user(), farms(), missions(), reports(), photos(), dataEntries()

// Modules/Portail/Models/Farm.php (ou Modules/Technitian/Models/)
// Relations: client(), technician(), missions(), reports(), photos(), dataEntries()

// Modules/Technitian/Models/Mission.php
// Relations: technician(), farm()

// Modules/Portail/Models/Report.php
// Relations: farm(), client(), technician(), validator()

// Modules/Portail/Models/Photo.php
// Relations: farm(), client(), technician()

// Modules/Technitian/Models/DataEntry.php
// Relations: farm(), client(), technician()

// App\Models\MarketPrice.php
// Pas de relation business()

// App\Models\Message.php
// Relations: sender(), receiver(), parent()

// Modules/Portail/Models/Subscription.php
// Relations: client()
```

---

## 3. Contrôleurs

### Espace Client (`Modules/Portail/Http/Controllers/`)

| Contrôleur                   | Méthodes                       | Routes                       |
| ---------------------------- | ------------------------------ | ---------------------------- |
| `PortailDashboardController` | `index()`                      | `admin.portail.index`        |
| `GalleryController`          | `index()`, `show()`            | `admin.portail.gallery`      |
| `ReportController`           | `index()`, `download()`        | `admin.portail.reports`      |
| `MessageController`          | `index()`, `store()`, `show()` | `admin.portail.messages`     |
| `SubscriptionController`     | `index()`                      | `admin.portail.subscription` |

### Espace Technicien (`Modules/Technitian/Http/Controllers/`)

| Contrôleur                      | Méthodes                         | Routes                      |
| ------------------------------- | -------------------------------- | --------------------------- |
| `TechnitianDashboardController` | `index()`                        | `admin.technitian.index`    |
| `MissionController`             | `index()`, `show()`, `update()`  | `admin.technitian.missions` |
| `ReportController`              | `create()`, `store()`, `index()` | `admin.technitian.reports`  |
| `PhotoController`               | `create()`, `store()`, `index()` | `admin.technitian.photos`   |
| `DataEntryController`           | `create()`, `store()`, `index()` | `admin.technitian.data`     |
| `CalendarController`            | `index()`                        | `admin.technitian.calendar` |

### Espace Admin (`App\Http\Controllers\Admin\` ou modules dédiés)

| Contrôleur                   | Méthodes                                                            | Routes                     |
| ---------------------------- | ------------------------------------------------------------------- | -------------------------- |
| `AdminDashboardController`   | `index()`                                                           | `admin.dashboard`          |
| `ClientController`           | `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()` | CRUD clients               |
| `TechnicianController`       | `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()` | CRUD techniciens           |
| `FarmController`             | `index()`, `create()`, `store()`, `edit()`, `update()`              | CRUD exploitations         |
| `ReportValidationController` | `index()`, `show()`, `validate()`, `reject()`                       | `admin.reports.validation` |
| `PhotoValidationController`  | `index()`, `approve()`, `reject()`                                  | `admin.photos.validation`  |
| `DataValidationController`   | `index()`, `validate()`, `reject()`                                 | `admin.data.validation`    |
| `MarketPriceController`      | `index()`, `create()`, `store()`, `edit()`, `update()`              | `admin.market-prices`      |
| `SubscriptionController`     | `index()`, `edit()`, `update()`                                     | `admin.subscriptions`      |

---

## 4. Routes

### Espace Client (Portail) — `Routes/web.php`

```php
Route::middleware(['web', 'auth', 'feature:portail'])
    ->prefix('admin/portail')
    ->name('admin.portail.')
    ->group(function () {
        Route::get('/', [PortailDashboardController::class, 'index'])->name('index');
        Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
        Route::get('/messages', [MessageController::class, 'index'])->name('messages');
        Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription');
    });
```

### Espace Technicien — `Routes/web.php`

```php
Route::middleware(['web', 'auth', 'feature:technitian'])
    ->prefix('admin/technitian')
    ->name('admin.technitian.')
    ->group(function () {
        Route::get('/', [TechnitianDashboardController::class, 'index'])->name('index');
        Route::get('/missions', [MissionController::class, 'index'])->name('missions');
        Route::get('/missions/{mission}', [MissionController::class, 'show'])->name('missions.show');
        Route::post('/missions/{mission}/complete', [MissionController::class, 'update'])->name('missions.complete');
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/photos/create', [PhotoController::class, 'create'])->name('photos.create');
        Route::post('/photos', [PhotoController::class, 'store'])->name('photos.store');
        Route::get('/data/create', [DataEntryController::class, 'create'])->name('data.create');
        Route::post('/data', [DataEntryController::class, 'store'])->name('data.store');
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    });
```

---

## 5. Vues Blade

### Layout principal : `resources/views/layouts/app.blade.php`

(Layout SaaS existant — étendre avec `@extends('layouts.app')`)

### Espace Client (Portail) — `Resources/Views/portail/`

```
Resources/Views/portail/
├── dashboard.blade.php          # Tableau de bord client
├── gallery.blade.php            # Galerie photos géolocalisées
├── reports/
│   ├── index.blade.php          # Liste des rapports
│   └── show.blade.php           # Détail d'un rapport
├── messages/
│   ├── index.blade.php          # Messagerie
│   └── show.blade.php           # Conversation
└── subscription.blade.php       # Mon abonnement
```

### Espace Technicien — `Resources/Views/technitian/`

```
Resources/Views/technitian/
├── dashboard.blade.php          # Tableau de bord technicien
├── missions/
│   ├── index.blade.php          # Liste des missions
│   └── show.blade.php           # Détail mission
├── reports/
│   └── create.blade.php         # Formulaire dépôt rapport
├── photos/
│   └── create.blade.php         # Upload photos
├── data/
│   └── create.blade.php         # Saisie données agronomiques
└── calendar.blade.php           # Calendrier (rappel Teams-like)
```

### Espace Admin — `resources/views/admin/`

```
resources/views/admin/
├── dashboard.blade.php                   # Vue globale
├── clients/
│   ├── index.blade.php                   # Liste clients
│   ├── create.blade.php                  # Création client
│   └── edit.blade.php                    # Modification client
├── technicians/
│   ├── index.blade.php                   # Liste techniciens
│   ├── create.blade.php                  # Création technicien
│   └── edit.blade.php                    # Modification technicien
├── farms/
│   ├── index.blade.php                   # Liste exploitations
│   ├── create.blade.php                  # Création exploitation
│   └── edit.blade.php                    # Modification exploitation
├── reports/
│   ├── validation.blade.php              # Validation des rapports
│   └── preview.blade.php                 # Prévisualisation PDF
├── photos/
│   └── validation.blade.php              # Validation des photos
├── data/
│   └── validation.blade.php              # Validation des saisies
├── market-prices/
│   ├── index.blade.php                   # Prix du marché
│   └── edit.blade.php                    # Modification prix
└── subscriptions/
    └── index.blade.php                   # Gestion abonnements
```

---

## 6. Flux techniques

### Flux 1 : Dépôt et validation d'un rapport

```
Technicien → [POST] /admin/technitian/reports → Report.store()
  → Utilisation de ReportData pour validation
  → status: 'pending'
  → Notification admin (email + in-app)
Admin → [GET] /admin/reports/validation → liste pending
Admin → [POST] /admin/reports/{id}/validate → Report.validate()
  → status: 'validated', validated_by, validated_at
  → Notification client (email + in-app badge "Nouveau")
Client → [GET] /admin/portail/reports → liste avec badge
Client → [GET] /admin/portail/reports/{id}/download → téléchargement
```

### Flux 2 : Mise à jour du dashboard client

```
Technicien → [POST] /admin/technitian/data → DataEntry.store()
  → Utilisation de DataEntryData pour validation
  → status: 'pending'
Admin → [POST] /admin/data/{id}/validate → DataEntry.validate()
  → farm.crop_stage, farm.crop_stage_progress,
    farm.expected_harvest_date mis à jour
  → Dashboard client mis à jour automatiquement
```

### Flux 3 : Upload photos terrain

```
Technicien → [POST] /admin/technitian/photos → Photo.store()
  → Utilisation de PhotoData pour validation
  → GPS lu depuis le navigateur/mobile (navigator.geolocation)
  → is_visible_to_client: false
Admin → [POST] /admin/photos/{id}/approve → Photo.approve()
  → is_visible_to_client: true
Client → [GET] /admin/portail/gallery → photos visibles
```

---
