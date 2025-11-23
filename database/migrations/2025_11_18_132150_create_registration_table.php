<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            // Champs fixes (toujours présents)
            $table->string('nom');
            $table->string('prenom');
            $table->enum('sexe', ['M', 'F', 'X']);
            $table->date('date_naissance');
            $table->string('nationalite', 3)->default('BEL');
            $table->string('club')->nullable();

            // Gestion dossard et paiement
            $table->integer('bib_number')->nullable()->unique(); // Numéro de dossard
            $table->boolean('is_paid')->default(false);
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');

            // Infos complémentaires
            $table->string('code_uci')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
