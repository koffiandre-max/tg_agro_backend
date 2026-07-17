# Migrations Complétées - TG'AGRO Backend

## ✅ Tables créées avec succès

Toutes les migrations ont été exécutées avec succès. Voici la liste complète des tables créées :

### 1. **clients** (2026_07_14_110000_create_clients_table.php)

Informations spécifiques aux clients diaspora.

**Champs principaux :**

- `user_id` (FK → users)
- `country_of_residence`, `country_of_origin`, `city_of_residence`
- `id_document_type`, `id_document_number`
- `subscription_type` (basic/standard/premium)
- `subscription_expires_at`
- `total_investment`
- `notes`

### 2. **technicians** (2026_07_14_110001_create_technicians_table.php)

Informations spécifiques aux techniciens terrain.

**Champs principaux :**

- `user_id` (FK → users)
- `phone_secondary`, `location_base`
- `is_available`, `max_concurrent_missions`, `current_workload`
- `notes`

### 3. **farms** (2026_07_14_110002_create_farms_table.php)

Exploitations agricoles.

**Champs principaux :**

- `user_id` (FK → clients)
- `name`, `location`
- `latitude`, `longitude` (géolocalisation)
- `total_area_hectares`
- `culture_type`
- `status` (active/inactive/fallow)
- `expected_harvest_date`
- `crop_stage`, `crop_stage_progress`
- `last_visit_date`
- `assigned_technician_id` (FK → technicians)
- `notes`

### 4. **missions** (2026_07_14_110003_create_missions_table.php)

Missions terrain assignées aux techniciens.

**Champs principaux :**

- `technician_id` (FK → technicians)
- `farm_id` (FK → farms)
- `title`, `description`
- `scheduled_date`
- `completed_at`
- `status` (pending/in_progress/completed/cancelled)
- `notes`

### 5. **reports** (2026_07_14_110004_create_reports_table.php)

Rapports PDF générés par les techniciens.

**Champs principaux :**

- `farm_id` (FK → farms)
- `client_id` (FK → clients)
- `technician_id` (FK → users, nullable)
- `title`, `type` (monthly/soil_analysis/harvest/other)
- `file_path`, `file_original_name`, `file_size`
- `notes`
- `status` (pending/validated/rejected)
- `rejection_reason`
- `validated_by` (FK → users)
- `validated_at`
- `seen_by_client` (badge "Nouveau")

### 6. **photos** (2026_07_14_110005_create_photos_table.php)

Photos géolocalisées des exploitations.

**Champs principaux :**

- `farm_id` (FK → farms)
- `client_id` (FK → clients)
- `technician_id` (FK → users)
- `photo_path`, `thumbnail_path`
- `caption`
- `latitude`, `longitude` (GPS)
- `taken_at`
- `is_visible_to_client` (validation admin)
- `file_size`

### 7. **data_entries** (2026_07_14_110006_create_data_entries_table.php)

Saisies de données agronomiques.

**Champs principaux :**

- `farm_id` (FK → farms)
- `client_id` (FK → clients)
- `technician_id` (FK → users)
- `crop_stage`, `crop_stage_progress`
- `estimated_harvest_date`
- `inputs_used` (JSON)
- `observations`
- `weather_conditions`
- `status` (pending/validated/rejected)
- `validated_by` (FK → users)
- `validated_at`

### 8. **market_prices** (2026_07_14_110007_create_market_prices_table.php)

Prix du marché local.

**Champs principaux :**

- `product_name`, `unit` (kg/tonne/sac)
- `price_per_unit`
- `currency` (défaut: FCFA)
- `region`
- `recorded_at`
- `source`

### 9. **messages** (2026_07_14_110008_create_messages_table.php)

Messagerie interne.

**Champs principaux :**

- `user_id` (FK → users, destinataire)
- `sender_id` (FK → users, expéditeur)
- `subject`
- `message`
- `is_read`, `read_at`
- `parent_id` (FK → messages, pour les réponses)

### 10. **invoices** (2026_07_14_110009_create_invoices_table.php)

Factures clients.

**Champs principaux :**

- `user_id` (FK → users)
- `invoice_number` (unique)
- `client_name`, `client_email`, `client_address`
- `issue_date`, `due_date`
- `status` (draft/sent/paid/overdue/cancelled)
- `subtotal`, `tax_amount`, `total_amount`
- `notes`

### 11. **invoice_lines** (2026_07_14_110010_create_invoice_lines_table.php)

Lignes de facture.

**Champs principaux :**

- `invoice_id` (FK → invoices)
- `description`
- `quantity`
- `unit_price`, `total_price`

## 📊 Relations entre tables

```
users
├── clients (1:1)
│   └── farms (1:N)
│       ├── missions (1:N)
│       ├── reports (1:N)
│       ├── photos (1:N)
│       └── data_entries (1:N)
├── technicians (1:1)
│   └── missions (1:N via technician_id)
├── messages (1:N comme sender)
└── invoices (1:N)
    └── invoice_lines (1:N)
```

## 🔍 Vérification

Pour vérifier que les tables ont été créées :

```bash
php artisan migrate:status
```

Pour voir la liste des tables :

```bash
# MySQL
php artisan db:show

# Ou directement en SQL
mysql -u root -p tg_agro_backend -e "SHOW TABLES;"
```

## 🚀 Prochaines étapes

1. **Créer les modèles Eloquent** manquants :
    - `Modules\Portail\Models\Client`
    - `Modules\Technician\Models\Technician`
    - `Modules\Portail\Models\Mission`
    - `Modules\Portail\Models\DataEntry`
    - `Modules\Portail\Models\MarketPrice`
    - `App\Models\Message`
    - `Modules\Portail\Models\Invoice`
    - `Modules\Portail\Models\InvoiceLine`

2. **Créer les seeders** pour peupler les tables :
    - `ClientSeeder`
    - `TechnicianSeeder`
    - `FarmSeeder`
    - etc.

3. **Créer les contrôleurs** avec la logique métier :
    - Implémenter les TODOs dans les contrôleurs existants
    - Créer les contrôleurs manquants

4. **Tester les relations** :
    - Vérifier les foreign keys
    - Tester les cascades de suppression

## 📝 Notes importantes

- Toutes les tables utilisent le moteur InnoDB (par défaut dans Laravel)
- Les foreign keys sont configurées avec des actions ON DELETE appropriées :
    - `cascade` : suppression en cascade
    - `set null` : mise à NULL si la référence est supprimée
- Les timestamps (`created_at`, `updated_at`) sont présents sur toutes les tables
- Les types de données sont optimisés selon l'usage (decimal pour les montants, etc.)

## 🔄 Rollback si nécessaire

Si vous devez annuler les migrations :

```bash
# Rollback de 5 migrations
php artisan migrate:rollback --step=5

# Rollback de toutes les migrations
php artisan migrate:reset

# Ou supprimer toutes les tables et recommencer
php artisan migrate:fresh
```
