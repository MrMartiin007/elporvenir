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

        $filename = "inventario_{$anio}_{$mes}.xls";
        
        $headers = [
            "Content-type"        => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Usamos una tabla HTML que Excel puede leer nativamente para conservar el "estilo bonito" (colores, negritas)
        $html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>';
        $html .= '<body>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0">';
        // Cabeceras con estilo (Color verde esmeralda para combinar con el diseño)
        $html .= '<thead><tr>';
        $html .= '<th style="background-color: #10B981; color: white; font-weight: bold; width: 120px;">Código Producto</th>';
        $html .= '<th style="background-color: #10B981; color: white; font-weight: bold; width: 350px;">Descripción de Producto</th>';
        $html .= '<th style="background-color: #10B981; color: white; font-weight: bold; width: 120px;">Cantidad (Stock)</th>';
        $html .= '<th style="background-color: #10B981; color: white; font-weight: bold; width: 120px;">Precio Costo</th>';
        $html .= '<th style="background-color: #10B981; color: white; font-weight: bold; width: 120px;">Total</th>';
        $html .= '</tr></thead>';
        $html .= '<tbody>';

        foreach ($productos as $producto) {
            $costo = (float) optional($producto->ultimaEntrada)->precio_costo;
            $total = $producto->stock * $costo;
            
            $html .= '<tr>';
            $html .= '<td style="text-align: center;">' . htmlspecialchars($producto->codigo_producto) . '</td>';
            $html .= '<td>' . htmlspecialchars($producto->detalle_producto) . '</td>';
            $html .= '<td style="text-align: center; font-weight: bold;">' . $producto->stock . '</td>';
            $html .= '<td style="text-align: right;">Q' . number_format($costo, 2, '.', ',') . '</td>';
            $html .= '<td style="text-align: right; color: #10B981; font-weight: bold;">Q' . number_format($total, 2, '.', ',') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html, 200, $headers);
    }
}
