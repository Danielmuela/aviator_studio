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
        Schema::table('projets', function (Blueprint $table) {
            $table->string('fichier_audio_path')->nullable()->after('statut');
            $table->string('fichier_video_path')->nullable()->after('fichier_audio_path');
            $table->string('fichier_audio_nom_original')->nullable()->after('fichier_video_path');
            $table->string('fichier_video_nom_original')->nullable()->after('fichier_audio_nom_original');
            $table->bigInteger('fichier_audio_taille')->nullable()->after('fichier_video_nom_original');
            $table->bigInteger('fichier_video_taille')->nullable()->after('fichier_audio_taille');
            $table->timestamp('fichier_audio_uploaded_at')->nullable()->after('fichier_video_taille');
            $table->timestamp('fichier_video_uploaded_at')->nullable()->after('fichier_audio_uploaded_at');
            $table->boolean('fichiers_valides_admin')->default(false)->after('fichier_video_uploaded_at');
            $table->text('commentaire_admin_fichiers')->nullable()->after('fichiers_valides_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            $table->dropColumn([
                'fichier_audio_path',
                'fichier_video_path', 
                'fichier_audio_nom_original',
                'fichier_video_nom_original',
                'fichier_audio_taille',
                'fichier_video_taille',
                'fichier_audio_uploaded_at',
                'fichier_video_uploaded_at',
                'fichiers_valides_admin',
                'commentaire_admin_fichiers'
            ]);
        });
    }
};
