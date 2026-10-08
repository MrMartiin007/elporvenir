<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La tienda consulta "la última entrada de cada producto" en cada página
     * (relación latestOfMany y orden por precio). Sin índice en productos_id
     * esas consultas recorren toda la tabla entradas: con 3.700 productos el
     * orden por precio no llega a terminar.
     *
     * Es idempotente: si se recargan las tablas desde un volcado y los índices
     * se pierden, se puede volver a ejecutar sin errores.
     */
    public function up(): void
    {
        $this->addIndex('entradas', ['productos_id', 'id'], 'entradas_productos_id_id_index');
        $this->addIndex('entradas', ['productos_id', 'created_at'], 'entradas_productos_id_created_at_index');
        $this->addIndex('productos', ['updated_at'], 'productos_updated_at_index');
    }

    public function down(): void
    {
        $this->dropIndex('entradas', 'entradas_productos_id_id_index');
        $this->dropIndex('entradas', 'entradas_productos_id_created_at_index');
        $this->dropIndex('productos', 'productos_updated_at_index');
    }

    private function hasIndex(string $table, string $name): bool
    {
        return !empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name]));
    }

    private function addIndex(string $table, array $columns, string $name): void
    {
        if ($this->hasIndex($table, $name)) {
            return;
        }

        Schema::table($table, fn (Blueprint $t) => $t->index($columns, $name));
    }

    private function dropIndex(string $table, string $name): void
    {
        if (!$this->hasIndex($table, $name)) {
            return;
        }

        Schema::table($table, fn (Blueprint $t) => $t->dropIndex($name));
    }
};
