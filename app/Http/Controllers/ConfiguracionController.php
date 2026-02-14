<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TarifaEnvio;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    /**
     * Guardar una nueva tarifa de envío.
     * Siempre se crea una nueva y se mantiene el mismo nombre "Envío Estándar"
     * o se toma el nombre de la anterior si existe.
     */
    public function storeTarifa(Request $request)
    {
        $request->validate([
            'costo' => 'required|numeric|min:0',
        ]);

        try {
            // Obtener el nombre de la última tarifa o usar default
            $ultimaTarifa = TarifaEnvio::latest()->first();
            $nombre = $ultimaTarifa ? $ultimaTarifa->nombre : 'Envío Estándar';

            TarifaEnvio::create([
                'nombre' => $nombre,
                'costo' => $request->costo,
                'activo' => true,
            ]);

            return back()->with('success', 'Tarifa de envío actualizada correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar tarifa: ' . $e->getMessage());
        }
    }
}
