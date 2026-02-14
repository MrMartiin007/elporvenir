<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('datos_envios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedidos_id');
            $table->string('numero_guia');
            $table->string('comprobante'); // Ruta del archivo
            $table->string('tipo_comprobante'); // 'img' o 'pdf'
            $table->timestamps();

            $table->foreign('pedidos_id')->references('id')->on('pedidos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_envios');
    }
};
