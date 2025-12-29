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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura');
            $table->string('monto');
            $table->string('foto_fac')->nullable();
            $table->unsignedBigInteger('empresas_id');
            $table->string('estado'); // Pendiente, Pagada, etc.
            $table->timestamps();

            $table->foreign('empresas_id')->references('id')->on('empresas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
