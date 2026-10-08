<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Banners y video de la portada de la tienda, administrables desde el panel.
     * tipo = 'banner' (imagen principal que rota) | 'video' (sección de video).
     */
    public function up(): void
    {
        if (Schema::hasTable('banners')) {
            return;
        }

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 10)->default('banner');
            $table->string('titulo', 120);
            $table->string('subtitulo', 200)->nullable();
            $table->string('texto_boton', 30)->nullable();
            $table->string('enlace', 255)->nullable();
            $table->string('imagen')->nullable();        // banner: imagen de escritorio / video: póster
            $table->string('imagen_movil')->nullable();  // banner: imagen opcional para celular
            $table->string('video')->nullable();         // solo tipo video
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->dateTime('inicia_en')->nullable();
            $table->dateTime('termina_en')->nullable();
            $table->timestamps();

            $table->index(['tipo', 'activo', 'orden'], 'banners_tipo_activo_orden_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
