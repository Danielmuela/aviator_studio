<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->enum('etape', [
                'enregistrement',
                'mixage', 
                'mastering',
                'distribution_en_cours',
                'distribution_terminee'
            ])->default('enregistrement');
            $table->foreignId('artiste_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('responsable_id')->constrained('users')->onDelete('cascade');
            $table->date('date_debut')->nullable();
            $table->date('date_fin_prevue')->nullable();
            $table->date('date_fin_reelle')->nullable();
            $table->enum('statut', ['actif', 'termine', 'suspendu'])->default('actif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projets');
    }
}; 