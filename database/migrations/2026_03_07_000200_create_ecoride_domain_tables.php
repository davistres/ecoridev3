<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->createVoitureTable();
        $this->createCovoiturageTable();
        $this->createConfirmationTable();
        $this->createSatisfactionTable();
        $this->createFluxTable();
        $this->createContactTable();
    }

    public function down(): void
    {
        Schema::dropIfExists('contact');
        Schema::dropIfExists('flux');
        Schema::dropIfExists('satisfaction');
        Schema::dropIfExists('confirmation');
        Schema::dropIfExists('covoiturage');
        Schema::dropIfExists('voiture');
    }

    private function createVoitureTable(): void
    {
        if (Schema::hasTable('voiture')) {
            return;
        }

        Schema::create('voiture', function (Blueprint $table) {
            $table->id('voiture_id');
            $table->foreignId('user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('immat', 9)->unique();
            $table->date('date_first_immat');
            $table->string('brand', 12);
            $table->string('model', 24);
            $table->string('color', 12);
            $table->unsignedTinyInteger('n_place');
            $table->string('energie', 20);
            $table->softDeletes();

            $table->index('user_id', 'voiture_user_idx');
        });
    }

    private function createCovoiturageTable(): void
    {
        if (Schema::hasTable('covoiturage')) {
            return;
        }

        Schema::create('covoiturage', function (Blueprint $table) {
            $table->id('covoit_id');
            $table->foreignId('user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('voiture_id')
                ->constrained(table: 'voiture', column: 'voiture_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('departure_address', 120);
            $table->string('add_dep_address', 120)->nullable();
            $table->string('postal_code_dep', 6);
            $table->string('city_dep', 45);
            $table->string('arrival_address', 120);
            $table->string('add_arr_address', 120)->nullable();
            $table->string('postal_code_arr', 6);
            $table->string('city_arr', 45);
            $table->date('departure_date');
            $table->time('departure_time');
            $table->date('arrival_date');
            $table->time('arrival_time');
            $table->time('max_travel_time');
            $table->unsignedTinyInteger('n_tickets');
            $table->unsignedInteger('price');
            $table->boolean('eco_travel')->default(false);
            $table->boolean('trip_started')->default(false);
            $table->boolean('trip_completed')->default(false);
            $table->boolean('cancelled')->default(false);

            $table->index(
                ['postal_code_dep', 'postal_code_arr', 'cancelled', 'trip_started', 'departure_date'],
                'covoiturage_search_idx'
            );
            $table->index(['user_id', 'departure_date'], 'covoiturage_user_departure_idx');
            $table->index(['voiture_id', 'departure_date'], 'covoiturage_vehicle_departure_idx');
        });
    }

    private function createConfirmationTable(): void
    {
        if (Schema::hasTable('confirmation')) {
            return;
        }

        Schema::create('confirmation', function (Blueprint $table) {
            $table->id('conf_id');
            $table->foreignId('covoit_id')
                ->constrained(table: 'covoiturage', column: 'covoit_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('statut', 32)->default('En cours');
            $table->unsignedTinyInteger('n_conf');

            $table->index(['covoit_id', 'statut'], 'confirmation_trip_status_idx');
            $table->index(['user_id', 'statut'], 'confirmation_user_status_idx');
            $table->index(['covoit_id', 'user_id'], 'confirmation_trip_user_idx');
        });
    }

    private function createSatisfactionTable(): void
    {
        if (Schema::hasTable('satisfaction')) {
            return;
        }

        Schema::create('satisfaction', function (Blueprint $table) {
            $table->id('satisfaction_id');
            $table->foreignId('user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('covoit_id')
                ->constrained(table: 'covoiturage', column: 'covoit_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->boolean('feeling')->nullable();
            $table->text('comment')->nullable();
            $table->text('review')->nullable();
            $table->unsignedTinyInteger('note')->nullable();
            $table->date('date')->nullable();

            $table->unique(['user_id', 'covoit_id'], 'satisfaction_user_trip_unique');
            $table->index(['covoit_id', 'date'], 'satisfaction_trip_date_idx');
        });
    }

    private function createFluxTable(): void
    {
        if (Schema::hasTable('flux')) {
            return;
        }

        Schema::create('flux', function (Blueprint $table) {
            $table->id('flux_id');
            $table->foreignId('conf_id')
                ->nullable()
                ->constrained(table: 'confirmation', column: 'conf_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('montant_init');
            $table->integer('montant');
            $table->integer('result');
            $table->string('type', 32);
            $table->timestamp('date')->useCurrent();

            $table->index(['user_id', 'type', 'date'], 'flux_user_type_date_idx');
            $table->index('conf_id', 'flux_confirmation_idx');
        });
    }

    private function createContactTable(): void
    {
        if (Schema::hasTable('contact')) {
            return;
        }

        Schema::create('contact', function (Blueprint $table) {
            $table->id('contact_id');
            $table->string('nom', 18);
            $table->string('mail', 255);
            $table->string('sujet', 80);
            $table->text('message');
            $table->timestamp('date_envoi')->useCurrent();

            $table->index('date_envoi', 'contact_sent_at_idx');
        });
    }
};
