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

        // Archivo nativo de Excel sin uso masivo de librerías, puro PHP para evitar avisos "corrompidos"
        require_once app_path('Services/SimpleXLSXGen.php');

        $data = [
            [
                '<center><style bgcolor="#10B981" color="#ffffff"><b>Código Producto</b></style></center>',
                '<style bgcolor="#10B981" color="#ffffff"><b>Descripción de Producto</b></style>',
                '<center><style bgcolor="#10B981" color="#ffffff"><b>Cantidad (Stock)</b></style></center>',
                '<right><style bgcolor="#10B981" color="#ffffff"><b>Precio Costo</b></style></right>',
                '<right><style bgcolor="#10B981" color="#ffffff"><b>Total</b></style></right>'
            ]
        ];

        foreach ($productos as $producto) {
            $costo = (float) optional($producto->ultimaEntrada)->precio_costo;
            $total = $producto->stock * $costo;
            
            $data[] = [
                '<center>' . $producto->codigo_producto . '</center>',
                $producto->detalle_producto,
                '<center><b>' . $producto->stock . '</b></center>',
                '<right>Q' . number_format($costo, 2, '.', ',') . '</right>',
                '<right><style color="#059669"><b>Q' . number_format($total, 2, '.', ',') . '</b></style></right>'
            ];
        }

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray( $data );
        // Dar formato a algunas columnas para que quepan mejor los nombres largos
        $xlsx->setColWidth(2, 45); // Descripción

        $filename = "inventario_{$anio}_{$mes}.xlsx";

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
