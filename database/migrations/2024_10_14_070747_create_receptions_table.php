<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receptions', function (Blueprint $table) {
            $table->string('BonReception')->primary(); // Clé primaire
            $table->string('CodeMateriel'); // Assurez-vous que c'est string
            $table->foreign('CodeMateriel')->references('CodeMateriel')->on('materiels')->cascadeOnDelete(); // Clé étrangère
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Clé étrangère
            $table->integer('QuantiteRecu'); // Quantité reçue
            $table->date('DateReception'); // Date de réception
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receptions');
    }
};