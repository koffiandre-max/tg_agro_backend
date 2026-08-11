<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\DataEntry;
use App\Models\Farm;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\MarketPrice;
use App\Models\Message;
use App\Models\Mission;
use App\Models\Photo;
use App\Models\Report;
use App\Models\Subscription;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Appeler le UserSeeder pour créer les users de base
        // UserSeeder::class;

        // Récupérer les users
        $admin = User::where('email', 'admin@tginvest.com')->first();
        $technician1 = User::where('email', 'technicien1@tginvest.com')->first();
        $technician2 = User::where('email', 'technicien2@tginvest.com')->first();
        $client1 = User::where('email', 'client1@tginvest.com')->first();
        $client2 = User::where('email', 'client2@tginvest.com')->first();

        // Créer les profils clients
        $clientProfile1 = Client::create([
            'user_id' => $client1->id,
            'country_of_residence' => 'France',
            'country_of_origin' => 'Côte d\'Ivoire',
            'city_of_residence' => 'Paris',
            'id_document_type' => 'Passeport',
            'id_document_number' => 'PASS123456',
            'subscription_type' => 'premium',
            'subscription_expires_at' => Carbon::now()->addYear(),
            'total_investment' => 150000,
            'notes' => 'Client VIP, investisseur depuis 2020',
        ]);

        $clientProfile2 = Client::create([
            'user_id' => $client2->id,
            'country_of_residence' => 'Belgique',
            'country_of_origin' => 'Côte d\'Ivoire',
            'city_of_residence' => 'Bruxelles',
            'id_document_type' => 'Carte d\'identité',
            'id_document_number' => 'ID789012',
            'subscription_type' => 'standard',
            'subscription_expires_at' => Carbon::now()->addMonths(6),
            'total_investment' => 75000,
            'notes' => 'Client standard',
        ]);

        // Créer les profils techniciens
        $technicianProfile1 = Technician::create([
            'user_id' => $technician1->id,
            'phone_secondary' => '+225 07 11 11 11 12',
            'location_base' => 'Abidjan, Cocody',
            'is_available' => true,
            'max_concurrent_missions' => 5,
            'current_workload' => 2,
            'notes' => 'Spécialiste en cultures maraîchères',
        ]);

        $technicianProfile2 = Technician::create([
            'user_id' => $technician2->id,
            'phone_secondary' => '+225 07 22 22 22 23',
            'location_base' => 'Bouaké',
            'is_available' => true,
            'max_concurrent_missions' => 5,
            'current_workload' => 1,
            'notes' => 'Expert en cultures pérennes (cacao, café)',
        ]);

        // Créer des farms pour client1 (3 farms)
        $farm1Client1 = Farm::create([
            'user_id' => $client1->id,
            'name' => 'Ferme de Yamoussoukro',
            'location' => 'Yamoussoukro, Côte d\'Ivoire',
            'latitude' => 6.8276,
            'longitude' => -5.2893,
            'total_area_hectares' => 25.5,
            'culture_type' => 'Cacao',
            'status' => 'active',
            'expected_harvest_date' => Carbon::now()->addMonths(3),
            'crop_stage' => 'croissance',
            'crop_stage_progress' => 65,
            'last_visit_date' => Carbon::now()->subDays(15),
            'assigned_technician_id' => $technicianProfile1->id,
            'notes' => 'Ferme principale du client',
        ]);

        $farm2Client1 = Farm::create([
            'user_id' => $client1->id,
            'name' => 'Plantation de Man',
            'location' => 'Man, Côte d\'Ivoire',
            'latitude' => 7.4123,
            'longitude' => -7.5534,
            'total_area_hectares' => 40.0,
            'culture_type' => 'Café',
            'status' => 'active',
            'expected_harvest_date' => Carbon::now()->addMonths(4),
            'crop_stage' => 'floraison',
            'crop_stage_progress' => 45,
            'last_visit_date' => Carbon::now()->subDays(20),
            'assigned_technician_id' => $technicianProfile1->id,
            'notes' => 'Plantation en altitude',
        ]);

        $farm3Client1 = Farm::create([
            'user_id' => $client1->id,
            'name' => 'Exploitation maraîchère',
            'location' => 'Abidjan, Bingerville',
            'latitude' => 5.3612,
            'longitude' => -3.8833,
            'total_area_hectares' => 5.0,
            'culture_type' => 'Légumes',
            'status' => 'active',
            'expected_harvest_date' => Carbon::now()->addWeeks(2),
            'crop_stage' => 'maturation',
            'crop_stage_progress' => 85,
            'last_visit_date' => Carbon::now()->subDays(5),
            'assigned_technician_id' => $technicianProfile2->id,
            'notes' => 'Culture maraîchère intensive',
        ]);

        // Créer des farms pour client2 (2 farms)
        $farm1Client2 = Farm::create([
            'user_id' => $client2->id,
            'name' => 'Ferme de Korhogo',
            'location' => 'Korhogo, Côte d\'Ivoire',
            'latitude' => 9.4580,
            'longitude' => -5.6293,
            'total_area_hectares' => 30.0,
            'culture_type' => 'Coton',
            'status' => 'active',
            'expected_harvest_date' => Carbon::now()->addMonths(5),
            'crop_stage' => 'semis',
            'crop_stage_progress' => 20,
            'last_visit_date' => Carbon::now()->subDays(30),
            'assigned_technician_id' => $technicianProfile2->id,
            'notes' => 'Ferme en phase de démarrage',
        ]);

        $farm2Client2 = Farm::create([
            'user_id' => $client2->id,
            'name' => 'Plantation d\'hévéa',
            'location' => 'San-Pédro, Côte d\'Ivoire',
            'latitude' => 4.7485,
            'longitude' => -6.6363,
            'total_area_hectares' => 50.0,
            'culture_type' => 'Hévéa',
            'status' => 'fallow',
            'expected_harvest_date' => Carbon::now()->addYears(2),
            'crop_stage' => null,
            'crop_stage_progress' => 0,
            'last_visit_date' => Carbon::now()->subMonths(3),
            'assigned_technician_id' => null,
            'notes' => 'En jachère pour régénération',
        ]);

        // Créer des missions pour technician1
        Mission::create([
            'technician_id' => $technicianProfile1->id,
            'farm_id' => $farm1Client1->id,
            'title' => 'Inspection trimestrielle - Ferme de Yamoussoukro',
            'description' => 'Inspection complète de la ferme, vérification de l\'état des cultures',
            'scheduled_date' => Carbon::now()->addDays(7),
            'status' => 'pending',
            'notes' => 'Prévoir équipement de mesure',
        ]);

        Mission::create([
            'technician_id' => $technicianProfile1->id,
            'farm_id' => $farm2Client1->id,
            'title' => 'Récolte café - Plantation de Man',
            'description' => 'Supervision de la récolte de café',
            'scheduled_date' => Carbon::now()->addDays(14),
            'status' => 'in_progress',
            'notes' => 'Coordonner avec les travailleurs locaux',
        ]);

        // Créer des missions pour technician2
        Mission::create([
            'technician_id' => $technicianProfile2->id,
            'farm_id' => $farm3Client1->id,
            'title' => 'Récolte légumes - Exploitation maraîchère',
            'description' => 'Récolte et conditionnement des légumes',
            'scheduled_date' => Carbon::now()->addDays(3),
            'status' => 'pending',
            'notes' => 'Prévoir les emballages',
        ]);

        Mission::create([
            'technician_id' => $technicianProfile2->id,
            'farm_id' => $farm1Client2->id,
            'title' => 'Préparation semis - Ferme de Korhogo',
            'description' => 'Préparation des champs pour les semis de coton',
            'scheduled_date' => Carbon::now()->addDays(10),
            'status' => 'pending',
            'notes' => 'Vérifier les intrants',
        ]);

        // Créer des rapports
        $report1 = Report::create([
            'farm_id' => $farm1Client1->id,
            'client_id' => $clientProfile1->id,
            'technician_id' => $technician1->id,
            'title' => 'Rapport mensuel - Octobre 2026',
            'type' => 'monthly',
            'file_path' => 'reports/rapport_octobre_2026.pdf',
            'file_original_name' => 'rapport_octobre_2026.pdf',
            'file_size' => 2048576,
            'notes' => 'Rapport mensuel d\'activité',
            'status' => 'validated',
            'validated_by' => $admin->id,
            'validated_at' => Carbon::now()->subDays(2),
            'seen_by_client' => true,
        ]);

        $report2 = Report::create([
            'farm_id' => $farm2Client1->id,
            'client_id' => $clientProfile1->id,
            'technician_id' => $technician1->id,
            'title' => 'Analyse de sol - Plantation de Man',
            'type' => 'soil_analysis',
            'file_path' => 'reports/analyse_sol_man.pdf',
            'file_original_name' => 'analyse_sol_man.pdf',
            'file_size' => 1572864,
            'notes' => 'Analyse complète du sol',
            'status' => 'pending',
            'seen_by_client' => false,
        ]);

        // Créer des photos
        Photo::create([
            'farm_id' => $farm1Client1->id,
            'client_id' => $clientProfile1->id,
            'technician_id' => $technician1->id,
            'photo_path' => 'photos/farm1_photo1.jpg',
            'thumbnail_path' => 'photos/thumbnails/farm1_photo1_thumb.jpg',
            'caption' => 'Vue générale de la ferme',
            'latitude' => 6.8276,
            'longitude' => -5.2893,
            'taken_at' => Carbon::now()->subDays(10),
            'is_visible_to_client' => true,
            'file_size' => 524288,
        ]);

        Photo::create([
            'farm_id' => $farm1Client1->id,
            'client_id' => $clientProfile1->id,
            'technician_id' => $technician1->id,
            'photo_path' => 'photos/farm1_photo2.jpg',
            'thumbnail_path' => 'photos/thumbnails/farm1_photo2_thumb.jpg',
            'caption' => 'Cultures de cacao en croissance',
            'latitude' => 6.8277,
            'longitude' => -5.2894,
            'taken_at' => Carbon::now()->subDays(10),
            'is_visible_to_client' => true,
            'file_size' => 489664,
        ]);

        // Créer des data_entries
        DataEntry::create([
            'farm_id' => $farm1Client1->id,
            'client_id' => $clientProfile1->id,
            'technician_id' => $technician1->id,
            'crop_stage' => 'croissance',
            'crop_stage_progress' => 65,
            'estimated_harvest_date' => Carbon::now()->addMonths(3),
            'inputs_used' => json_encode(['engrais' => 'NPK 15-15-15', 'pesticide' => 'Deltaméthrine']),
            'observations' => 'Bonne croissance des plants, pas de parasites observés',
            'weather_conditions' => 'Ensoleillé, 28°C',
            'status' => 'validated',
            'validated_by' => $admin->id,
            'validated_at' => Carbon::now()->subDays(1),
        ]);

        // Créer des subscriptions
        Subscription::create([
            'user_id' => $client1->id,
            'type' => 'premium',
            'amount' => 500,
            'currency' => 'EUR',
            'start_date' => Carbon::now()->subMonths(6),
            'end_date' => Carbon::now()->addMonths(6),
            'status' => 'active',
            'payment_method' => 'Carte bancaire',
            'payment_reference' => 'SUB123456',
            'auto_renew' => true,
        ]);

        Subscription::create([
            'user_id' => $client2->id,
            'type' => 'standard',
            'amount' => 250,
            'currency' => 'EUR',
            'start_date' => Carbon::now()->subMonths(3),
            'end_date' => Carbon::now()->addMonths(3),
            'status' => 'active',
            'payment_method' => 'PayPal',
            'payment_reference' => 'SUB789012',
            'auto_renew' => true,
        ]);

        // Créer des market_prices (produits vivriers - Côte d'Ivoire)
        $marketProducts = [
            ['product_name' => 'Cacao', 'unit' => 'kg', 'price_per_unit' => 850, 'region' => 'Abidjan', 'source' => 'Conseil Café-Cacao'],
            ['product_name' => 'Café', 'unit' => 'kg', 'price_per_unit' => 1200, 'region' => 'Man', 'source' => 'Conseil Café-Cacao'],
            ['product_name' => 'Coton', 'unit' => 'kg', 'price_per_unit' => 450, 'region' => 'Korhogo', 'source' => 'Conseil Coton et Anacarde'],
            ['product_name' => 'Anacarde', 'unit' => 'kg', 'price_per_unit' => 700, 'region' => 'Bondoukou', 'source' => 'Conseil Coton et Anacarde'],
            ['product_name' => 'Igname', 'unit' => 'kg', 'price_per_unit' => 350, 'region' => 'Daloa', 'source' => 'MARCO'],
            ['product_name' => 'Manioc', 'unit' => 'kg', 'price_per_unit' => 200, 'region' => 'Yamoussoukro', 'source' => 'MARCO'],
            ['product_name' => 'Plantain', 'unit' => 'kg', 'price_per_unit' => 300, 'region' => 'Abobo', 'source' => 'MARCO'],
            ['product_name' => 'Banane', 'unit' => 'kg', 'price_per_unit' => 250, 'region' => 'Sinfra', 'source' => 'MARCO'],
            ['product_name' => 'Maïs', 'unit' => 'kg', 'price_per_unit' => 275, 'region' => 'Bouaké', 'source' => 'FENACOVICI'],
            ['product_name' => 'Riz local', 'unit' => 'kg', 'price_per_unit' => 500, 'region' => 'Gagnoa', 'source' => 'FENACOVICI'],
            ['product_name' => 'Arachide', 'unit' => 'kg', 'price_per_unit' => 600, 'region' => 'Katiola', 'source' => 'FENASOF'],
            ['product_name' => 'Tomate', 'unit' => 'kg', 'price_per_unit' => 400, 'region' => 'Azaguié', 'source' => 'MARCO'],
            ['product_name' => 'Piment', 'unit' => 'kg', 'price_per_unit' => 800, 'region' => 'Abengourou', 'source' => 'MARCO'],
            ['product_name' => 'Gombo', 'unit' => 'kg', 'price_per_unit' => 350, 'region' => 'Divo', 'source' => 'MARCO'],
            ['product_name' => 'Aubergine', 'unit' => 'kg', 'price_per_unit' => 300, 'region' => 'Tiassalé', 'source' => 'MARCO'],
            ['product_name' => 'Patate douce', 'unit' => 'kg', 'price_per_unit' => 220, 'region' => 'Sassandra', 'source' => 'MARCO'],
            ['product_name' => 'Ananas', 'unit' => 'kg', 'price_per_unit' => 320, 'region' => 'Bingerville', 'source' => 'MARCO'],
            ['product_name' => 'Mangue', 'unit' => 'kg', 'price_per_unit' => 280, 'region' => 'San-Pédro', 'source' => 'MARCO'],
            ['product_name' => 'Orange', 'unit' => 'kg', 'price_per_unit' => 400, 'region' => 'Bouna', 'source' => 'MARCO'],
            ['product_name' => 'Citron', 'unit' => 'kg', 'price_per_unit' => 500, 'region' => 'Grand-Bassam', 'source' => 'MARCO'],
        ];

        foreach ($marketProducts as $mp) {
            MarketPrice::create(array_merge($mp, [
                'currency' => 'FCFA',
                'recorded_at' => Carbon::now(),
            ]));
        }
        ]);

        // Créer des messages
        Message::create([
            'user_id' => $client1->id,
            'sender_id' => $admin->id,
            'subject' => 'Bienvenue sur TG Invest',
            'message' => 'Bonjour, bienvenue sur la plateforme TG Invest. Nous sommes ravis de vous accompagner dans vos projets agricoles.',
            'is_read' => true,
            'read_at' => Carbon::now()->subDays(5),
        ]);

        Message::create([
            'user_id' => $client2->id,
            'sender_id' => $admin->id,
            'subject' => 'Votre abonnement est actif',
            'message' => 'Votre abonnement standard est maintenant actif. Vous pouvez accéder à toutes les fonctionnalités.',
            'is_read' => false,
            'read_at' => null,
        ]);

        // Créer des invoices pour client1
        $invoice1 = Invoice::create([
            'user_id' => $client1->id,
            'invoice_number' => 'INV-2026-001',
            'client_name' => $client1->name,
            'client_email' => $client1->email,
            'client_address' => 'Paris, France',
            'issue_date' => Carbon::now()->subDays(30),
            'due_date' => Carbon::now()->addDays(30),
            'status' => 'paid',
            'subtotal' => 1000,
            'tax_amount' => 200,
            'total_amount' => 1200,
            'notes' => 'Abonnement Premium - 6 mois',
        ]);

        InvoiceLine::create([
            'invoice_id' => $invoice1->id,
            'description' => 'Abonnement Premium - 6 mois',
            'quantity' => 1,
            'unit_price' => 1000,
            'total_price' => 1000,
        ]);

        // Créer des invoices pour client2
        $invoice2 = Invoice::create([
            'user_id' => $client2->id,
            'invoice_number' => 'INV-2026-002',
            'client_name' => $client2->name,
            'client_email' => $client2->email,
            'client_address' => 'Bruxelles, Belgique',
            'issue_date' => Carbon::now()->subDays(15),
            'due_date' => Carbon::now()->addDays(45),
            'status' => 'sent',
            'subtotal' => 500,
            'tax_amount' => 100,
            'total_amount' => 600,
            'notes' => 'Abonnement Standard - 3 mois',
        ]);

        InvoiceLine::create([
            'invoice_id' => $invoice2->id,
            'description' => 'Abonnement Standard - 3 mois',
            'quantity' => 1,
            'unit_price' => 500,
            'total_price' => 500,
        ]);

        $this->command->info('✅ Données de test créées avec succès !');
        $this->command->info('👥 Utilisateurs créés :');
        $this->command->info('   - Admin: admin@tginvest.com / password123');
        $this->command->info('   - Technicien 1: technicien1@tginvest.com / password123');
        $this->command->info('   - Technicien 2: technicien2@tginvest.com / password123');
        $this->command->info('   - Client 1: client1@tginvest.com / password123');
        $this->command->info('   - Client 2: client2@tginvest.com / password123');
        $this->command->info('🌾 5 fermes créées (3 pour client1, 2 pour client2)');
        $this->command->info('📋 4 missions créées');
        $this->command->info('📄 2 rapports créés');
        $this->command->info('📸 2 photos créées');
        $this->command->info('📊 1 data_entry créée');
        $this->command->info('💳 2 abonnements créés');
        $this->command->info('💰 3 prix du marché créés');
        $this->command->info('💬 2 messages créés');
        $this->command->info('🧾 2 factures créées');
    }
}
