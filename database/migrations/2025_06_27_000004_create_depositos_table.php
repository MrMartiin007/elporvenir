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
        Schema::create('depositos', function (Blueprint $table) {
            $table->id();
            $table->string('no_deposito');
            $table->date('fecha_deposito');
            $table->string('foto_deposito')->nullable();
            $table->unsignedBigInteger('facturas_id');
            $table->timestamps();

            $table->foreign('facturas_id')->references('id')->on('facturas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depositos');
    }
};
