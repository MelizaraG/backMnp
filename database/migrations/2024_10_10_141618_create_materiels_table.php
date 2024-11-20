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
        Schema::create('materiels', function (Blueprint $table) {
            $table->string('CodeMateriel') -> primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('Designation');
            $table->string('Type');
            $table->integer('Quantite');
            $table->string('PrixUnitaire');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiels');
    }
};
