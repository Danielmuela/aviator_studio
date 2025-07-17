<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projet_etapes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_id')->constrained()->onDelete('cascade');
            $table->enum('etape', [
                'enregistrement',
                'mixage', 
                'mastering',
                'distribution_en_cours',
                'distribution_terminee'
            ]);
            $table->foreignId('modifie_par')->constrained('users');
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projet_etapes');
    }
}; 