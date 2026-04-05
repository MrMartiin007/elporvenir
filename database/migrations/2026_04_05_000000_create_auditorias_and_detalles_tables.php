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
        // 1. Tabla de Sesiones (Auditorias Diarias)
        Schema::create('auditorias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('fecha_auditoria');
            $table->integer('cantidad_productos')->default(0);
            $table->decimal('total_auditado', 15, 2)->default(0);
            $table->boolean('estado')->default(1); // 1 = abierta, 0 = cerrada
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 2. Tabla de Detalles (Acciones/Productos auditados)
        Schema::create('detalle_auditorias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('auditorias_id')->constrained('auditorias')->onDelete('cascade');
            $table->foreignId('productos_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Stock
            $table->integer('stock_anterior')->default(0);
            $table->integer('stock_nuevo')->default(0);

            // Precios costo
            $table->decimal('precio_costo_anterior', 10, 2)->default(0);
            $table->decimal('precio_costo_nuevo', 10, 2)->default(0);

            // Precios venta
            $table->decimal('precio_venta_anterior', 10, 2)->default(0);
            $table->decimal('precio_venta_nuevo', 10, 2)->default(0);

            // Precios docena
            $table->decimal('precio_docena_anterior', 10, 2)->default(0);
            $table->decimal('precio_docena_nuevo', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_auditorias');
        Schema::dropIfExists('auditorias');
    }
};
