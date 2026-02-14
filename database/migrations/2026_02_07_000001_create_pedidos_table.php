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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_pedido', 50)->unique();

            // Información del Cliente
            $table->string('nombre_cliente', 255);
            $table->string('telefono_cliente', 20);
            $table->string('email_cliente', 255)->nullable();

            // Ubicación (Foreign keys a departamentos y municipios)
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->onDelete('set null');
            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->onDelete('set null');
            $table->text('direccion_cliente');
            $table->text('notas_cliente')->nullable();

            // Información del Pedido
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->decimal('envio', 10, 2);
            $table->integer('cantidad_productos');

            // Estado y Seguimiento
            $table->enum('estado', ['pendiente', 'confirmado', 'en_proceso', 'enviado', 'entregado', 'cancelado'])
                ->default('pendiente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
