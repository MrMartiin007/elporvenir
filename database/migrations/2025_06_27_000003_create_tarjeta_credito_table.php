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
        Schema::create('tarjeta_credito', function (Blueprint $table) {
            $table->id();
            $table->string('no_autorizacion');
            $table->string('foto_tc')->nullable();
            $table->unsignedBigInteger('facturas_id');
            $table->integer('estado')->default(1);
            $table->date('fecha_pago');
            $table->timestamps();

            $table->foreign('facturas_id')->references('id')->on('facturas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjeta_credito');
    }
};
