<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables du module Payment (Business Suite).
 *
 *  - suite_plans         : offres d'abonnement (mensuel / annuel).
 *  - suite_subscriptions : abonnements du tenant courant, avec option auto_renew.
 *  - suite_payments      : transactions (carte bancaire / mobile money via passerelle).
 *
 * "business_id" est indexé mais SANS contrainte FK afin de rester installable
 * dans n'importe quelle app Laravel hôte : le tenant est l'identifiant du
 * business quand la table "businesses" existe, sinon l'id de l'utilisateur.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Ancienne table placeholder du squelette SDK (remplacée par les tables ci-dessous).
        Schema::dropIfExists('payment');

        if (! Schema::hasTable('suite_plans')) {
            Schema::create('suite_plans', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 15, 2)->default(0);
                $table->string('currency', 10)->default('XOF');
                $table->unsignedSmallInteger('period_months')->default(1); // 1 = mensuel, 12 = annuel
                $table->json('features')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('suite_subscriptions')) {
            Schema::create('suite_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('plan_id')->nullable();
                $table->string('status', 30)->default('pending'); // pending|active|past_due|canceled|expired
                $table->unsignedSmallInteger('period_months')->default(1);
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('currency', 10)->default('XOF');
                $table->date('starts_at')->nullable();
                $table->date('ends_at')->nullable();
                $table->date('next_payment_at')->nullable();
                $table->boolean('auto_renew')->default(false);
                $table->string('payment_method', 30)->nullable(); // card|mobile_money
                $table->json('payment_details')->nullable();      // {provider, phone, last_reference}
                $table->timestamp('canceled_at')->nullable();
                $table->timestamps();

                $table->index(['business_id', 'status']);
            });
        }

        if (! Schema::hasTable('suite_payments')) {
            Schema::create('suite_payments', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->unique();
                $table->unsignedBigInteger('business_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('subscription_id')->nullable()->index();
                $table->unsignedBigInteger('plan_id')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('currency', 10)->default('XOF');
                $table->string('description')->nullable();
                $table->string('method', 30)->nullable();     // card|mobile_money
                $table->string('provider', 30)->nullable();   // simulate|fedapay|cinetpay
                $table->string('provider_reference')->nullable()->index();
                $table->string('status', 30)->default('pending'); // pending|succeeded|failed|canceled|refunded
                $table->string('payer_name', 120)->nullable();
                $table->string('payer_phone', 30)->nullable();
                $table->string('card_brand', 30)->nullable();
                $table->string('card_last4', 4)->nullable();
                $table->boolean('is_renewal')->default(false);
                $table->json('metadata')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('suite_payments');
        Schema::dropIfExists('suite_subscriptions');
        Schema::dropIfExists('suite_plans');
    }
};
