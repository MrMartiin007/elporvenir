<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $anio = $request->input('anio', 'todos');
        $mes  = $request->input('mes', 'todos');

        // ─── Años disponibles (basado en updated_at) ──────────────────────────
        $aniosDisponibles = Producto::selectRaw('YEAR(updated_at) as anio')
            ->distinct()
            ->orderByDesc('anio')
            ->pluck('anio')
            ->filter()
            ->values();

        if ($aniosDisponibles->isEmpty()) {
            $aniosDisponibles = collect([Carbon::now()->year]);
        }
        
        if ($anio !== 'todos' && !$aniosDisponibles->contains((int)$anio)) {
            $aniosDisponibles->push((int)$anio);
            $aniosDisponibles = $aniosDisponibles->sortDesc()->values();
        }

        // ─── Cargar productos con relaciones filtrando por fecha ────────────
        $query = Producto::with(['ultimaEntrada', 'marca']);
        
        if ($anio !== 'todos') {
            $query->whereYear('updated_at', $anio);
        }
        
        if ($mes !== 'todos') {
            $query->whereMonth('updated_at', $mes);
        }
        
        $todosLosProductos = $query->get();

        $costoPorProducto = [];
        foreach ($todosLosProductos as $p) {
            $costoPorProducto[$p->id] = (float) optional($p->ultimaEntrada)->precio_costo;
        }

        // ─── KPI Cards ───────────────────────────────────────────────────────
        $totalProductos  = $todosLosProductos->count();
        $stockTotal      = $todosLosProductos->sum(fn($p) => (int) $p->stock);
        $valorInventario = $todosLosProductos
            ->where('stock', '>', 0)
            ->sum(function ($p) use ($costoPorProducto) {
                return (int) $p->stock * $costoPorProducto[$p->id];
            });

        $productosEnCero = $todosLosProductos->where('stock', '<=', 0)->sortBy('detalle_producto')->values();
        $cantidadEnCero  = $productosEnCero->count();

        // ─── Top 10 productos con más stock ─────────────────────────────────
        $topProductos = $todosLosProductos
            ->where('stock', '>', 0)
            ->sortByDesc(fn($p) => (int) $p->stock)
            ->take(10)
            ->values();

        return view('inventario.index', compact(
            'anio',
            'mes',
            'aniosDisponibles',
            'totalProductos',
            'stockTotal',
            'valorInventario',
            'productosEnCero',
            'cantidadEnCero',
            'topProductos'
        ));
    }

    public function exportarExcel(Request $request)
    {
        $anio = $request->input('anio', 'todos');
        $mes  = $request->input('mes', 'todos');

        $query = Producto::with(['ultimaEntrada']);
        
        if ($anio !== 'todos') {
            $query->whereYear('updated_at', $anio);
        }
        
        if ($mes !== 'todos') {
            $query->whereMonth('updated_at', $mes);
        }
        
        $productos = $query->get();

        $filename = "inventario_{$anio}_{$mes}.csv";
        
        $headers = [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($productos) {
            $file = fopen('php://output', 'w');
            
            // BOM charset para Excel
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['Codigo Producto', 'Descripcion de Producto', 'Cantidad (Stock)', 'Precio Costo', 'Total'], ';');

            foreach ($productos as $producto) {
                $costo = (float) optional($producto->ultimaEntrada)->precio_costo;
                $total = $producto->stock * $costo;
                
                fputcsv($file, [
                    $producto->codigo_producto,
                    $producto->detalle_producto,
                    $producto->stock,
                    number_format($costo, 2, '.', ''),
                    number_format($total, 2, '.', '')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
