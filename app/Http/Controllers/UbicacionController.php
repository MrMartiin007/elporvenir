<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UbicacionController extends Controller
{
    /**
     * Mostrar vista principal con departamentos y municipios
     */
    public function index()
    {
        $buscar = request('buscar');

        // Eager load municipios para asegurar que se carguen
        $departamentos = Departamento::with([
            'municipios' => function ($query) {
                $query->orderBy('nombre', 'asc');
            }
        ])
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhereHas('municipios', function ($q) use ($buscar) {
                        $q->where('nombre', 'like', "%{$buscar}%");
                    });
            })
            ->orderBy('nombre', 'asc')
            ->get();

        return view('ubicacion.index', compact('departamentos'));
    }

    /**
     * Crear departamento
     */
    public function storeDepartamento(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:departamentos,nombre'
        ], [
            'nombre.required' => 'El nombre del departamento es obligatorio',
            'nombre.unique' => 'Este departamento ya existe'
        ]);

        Departamento::create([
            'nombre' => $validated['nombre'],
            'activo' => $request->has('activo') ? 1 : 0
        ]);

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Departamento creado exitosamente');
    }

    /**
     * Actualizar departamento
     */
    public function updateDepartamento(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:departamentos,nombre,' . $id
        ], [
            'nombre.required' => 'El nombre del departamento es obligatorio',
            'nombre.unique' => 'Este departamento ya existe'
        ]);

        $departamento = Departamento::findOrFail($id);
        $departamento->update([
            'nombre' => $validated['nombre'],
            'activo' => $request->has('activo') ? 1 : 0
        ]);

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Departamento actualizado exitosamente');
    }

    /**
     * Eliminar departamento
     */
    public function destroyDepartamento($id)
    {
        $departamento = Departamento::findOrFail($id);

        // Validar si tiene municipios
        $cantidadMunicipios = $departamento->municipios()->count();
        if ($cantidadMunicipios > 0) {
            return redirect()->route('ubicaciones.index')
                ->with('error', "Este departamento tiene {$cantidadMunicipios} municipios. Elimínalos o desactívalos primero.");
        }

        $departamento->delete();

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Departamento eliminado exitosamente');
    }

    /**
     * Crear municipio
     */
    public function storeMunicipio(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'departamento_id' => 'required|exists:departamentos,id'
        ], [
            'nombre.required' => 'El nombre del municipio es obligatorio',
            'departamento_id.required' => 'Debes seleccionar un departamento'
        ]);

        Municipio::create([
            'nombre' => $validated['nombre'],
            'departamento_id' => $validated['departamento_id'],
            'activo' => $request->has('activo') ? 1 : 0
        ]);

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Municipio creado exitosamente');
    }

    /**
     * Actualizar municipio
     */
    public function updateMunicipio(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'departamento_id' => 'required|exists:departamentos,id'
        ], [
            'nombre.required' => 'El nombre del municipio es obligatorio',
            'departamento_id.required' => 'Debes seleccionar un departamento'
        ]);

        $municipio = Municipio::findOrFail($id);
        $municipio->update([
            'nombre' => $validated['nombre'],
            'departamento_id' => $validated['departamento_id'],
            'activo' => $request->has('activo') ? 1 : 0
        ]);

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Municipio actualizado exitosamente');
    }

    /**
     * Eliminar municipio
     */
    public function destroyMunicipio($id)
    {
        $municipio = Municipio::findOrFail($id);

        // Validar si tiene pedidos asociados
        $cantidadPedidos = $municipio->pedidos()->count();
        if ($cantidadPedidos > 0) {
            return redirect()->route('ubicaciones.index')
                ->with('error', 'Este municipio tiene pedidos asociados y no puede eliminarse.');
        }

        $municipio->delete();

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Municipio eliminado exitosamente');
    }

    /**
     * Toggle estado activo/inactivo (AJAX)
     */
    public function toggleActivo($tipo, $id)
    {
        try {
            if ($tipo === 'departamento') {
                $item = Departamento::findOrFail($id);
            } else {
                $item = Municipio::findOrFail($id);
            }

            $item->activo = !$item->activo;
            $item->save();

            return response()->json([
                'success' => true,
                'activo' => $item->activo,
                'message' => ucfirst($tipo) . ' ' . ($item->activo ? 'activado' : 'desactivado') . ' exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ], 500);
        }
    }
}
