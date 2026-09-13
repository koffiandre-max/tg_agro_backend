<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Journal des emails envoyés par le module SendEmail.
 *
 * "business_id" est indexé mais SANS contrainte FK afin de rester installable
 * dans n'importe quelle app Laravel hôte.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Ancienne table placeholder du squelette SDK.
        Schema::dropIfExists('send_email');

        if (! Schema::hasTable('suite_email_logs')) {
            Schema::create('suite_email_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('to_email');
                $table->string('to_name', 120)->nullable();
                $table->string('subject');
                $table->longText('body');
                $table->boolean('is_html')->default(true);
                $table->string('status', 20)->default('pending'); // pending|sent|failed
                $table->text('error')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->index(['business_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('suite_email_logs');
    }
};

