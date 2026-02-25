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
        Schema::create('producto_eliminado_ventas', function (Blueprint $table) {
            $table->id();

            // Relación con la venta de donde se eliminó
            $table->unsignedBigInteger('ventas_id')->nullable();
            $table->foreign('ventas_id')->references('id')->on('ventas')->onDelete('set null');

            // Relación con el producto eliminado
            $table->unsignedBigInteger('productos_id')->nullable();
            $table->foreign('productos_id')->references('id')->on('productos')->onDelete('set null');

            // Usuario cajero que realizó la eliminación
            $table->unsignedBigInteger('users_id')->nullable();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('set null');

            // Datos contables del momento de la eliminación
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('importe_total', 10, 2); // cantidad * precio_unitario

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_eliminado_ventas');
    }
};
