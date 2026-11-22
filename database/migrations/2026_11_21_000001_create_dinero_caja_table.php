<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dinero que el administrador deja en caja cada día (una sola fila por fecha).
     * Se captura al cerrar una venta; si se vuelve a cerrar ese día se modifica el mismo registro.
     * Es idempotente: si la tabla ya existe no hace nada.
     */
    public function up(): void
    {
        if (Schema::hasTable('dinero_caja')) {
            return;
        }

        Schema::create('dinero_caja', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->unique();
            $table->decimal('monto', 12, 2)->default(0);
            $table->unsignedBigInteger('users_id')->nullable();
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dinero_caja');
    }
};
