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
        Schema::create('tarifas_envios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Estándar, Express, Interior
            $table->decimal('costo', 10, 2);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar tarifa por defecto
        DB::table('tarifas_envios')->insert([
            'nombre' => 'Envío Estándar',
            'costo' => 35.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifas_envios');
    }
};
