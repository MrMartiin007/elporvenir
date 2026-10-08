<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Códigos de barras ADICIONALES de un producto (el mismo producto que llega con otro código en otro viaje).
     * El código principal sigue en productos.codigo_producto. Un código no puede repetirse: aquí es único,
     * y la aplicación además lo compara contra los códigos principales.
     */
    public function up(): void
    {
        if (Schema::hasTable('producto_codigos')) {
            return;
        }

        Schema::create('producto_codigos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productos_id')->constrained('productos')->cascadeOnDelete();
            $table->string('codigo', 100)->unique();
            $table->timestamps();

            $table->index('productos_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_codigos');
    }
};
