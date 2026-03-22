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
        $anio = $request->input('anio', Carbon::now()->year);
        $mes  = $request->input('mes', 'todos');

        // ─── Años disponibles (basado en entradas) ───────────────────────────
        $aniosDisponibles = Entrada::selectRaw('YEAR(fecha_ingreso) as anio')
            ->distinct()
            ->orderByDesc('anio')
            ->pluck('anio')
            ->filter()
            ->values();

        // Si no hay entradas, mostrar el año actual
        if ($aniosDisponibles->isEmpty()) {
            $aniosDisponibles = collect([Carbon::now()->year]);
        }

        // ─── KPI Cards ───────────────────────────────────────────────────────

        // 1. Total de productos registrados
        $totalProductos = Producto::count();

        // 2. Stock total general (suma de todas las unidades)
        $stockTotal = Producto::sum('stock');

        // 3. Valor del inventario en Q (stock × precio_costo de la última entrada)
        //    Usamos un join para obtener el precio_costo de la entrada más reciente por producto
        $valorInventario = DB::table('productos as p')
            ->join('entradas as e', function ($join) {
                $join->on('e.id', '=', DB::raw(
                    '(SELECT id FROM entradas WHERE productos_id = p.id ORDER BY id DESC LIMIT 1)'
                ));
            })
            ->where('p.stock', '>', 0)
            ->selectRaw('SUM(p.stock * CAST(e.precio_costo AS DECIMAL(10,2))) as total')
            ->value('total') ?? 0;

        // 4. Productos con stock = 0 (alerta)
        $productosEnCero = Producto::with('marca')
            ->where('stock', 0)
            ->orderBy('detalle_producto')
            ->get();

        $cantidadEnCero = $productosEnCero->count();

        // ─── Top 10 productos con más stock ─────────────────────────────────
        $topProductos = Producto::with(['marca', 'ultimaEntrada'])
            ->where('stock', '>', 0)
            ->orderByDesc('stock')
            ->take(10)
            ->get();

        // ─── Stock agrupado por marca (para gráfica donut) ──────────────────
        $stockPorMarca = Producto::with('marca')
            ->select('marcas_id', DB::raw('SUM(stock) as total_stock'))
            ->groupBy('marcas_id')
            ->where('stock', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'marca' => optional($item->marca)->nombre_marca ?? 'Sin marca',
                    'stock' => (int) $item->total_stock,
                ];
            })
            ->sortByDesc('stock')
            ->values();

        // ─── Gráfica de líneas: valor de entradas (cantidad × precio_costo) ─
        // Inicializar estructura
        $datosGraficoCosto = [];
        if ($mes === 'todos') {
            for ($i = 1; $i <= 12; $i++) {
                $datosGraficoCosto[$i] = 0;
            }
        } else {
            $diasEnMes = Carbon::createFromDate($anio, $mes)->daysInMonth;
            for ($i = 1; $i <= $diasEnMes; $i++) {
                $datosGraficoCosto[$i] = 0;
            }
        }

        // Obtener entradas del período seleccionado
        $queryEntradas = Entrada::whereYear('fecha_ingreso', $anio);
        if ($mes !== 'todos') {
            $queryEntradas->whereMonth('fecha_ingreso', $mes);
        }
        $entradasPeriodo = $queryEntradas->get();

        $totalEntradasPeriodo   = 0;
        $cantidadEntradasPeriodo = 0;

        foreach ($entradasPeriodo as $entrada) {
            $valorEntrada = (float) $entrada->cantidad * (float) $entrada->precio_costo;
            $totalEntradasPeriodo += $valorEntrada;
            $cantidadEntradasPeriodo++;

            try {
                $fecha = Carbon::parse($entrada->fecha_ingreso);
                $key   = ($mes === 'todos') ? $fecha->month : $fecha->day;
                if (isset($datosGraficoCosto[$key])) {
                    $datosGraficoCosto[$key] += $valorEntrada;
                }
            } catch (\Exception $e) {
                // Fecha inválida — ignorar
            }
        }

        // Mejor y peor mes/día
        $mesesNombre = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo',  6 => 'Junio',   7 => 'Julio',  8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $valoresConDatos = array_filter($datosGraficoCosto, fn($v) => $v > 0);

        $mejorKey   = '-';
        $mejorMonto = 0;
        $peorKey    = '-';
        $peorMonto  = 0;

        if (count($valoresConDatos) > 0) {
            $mejorMonto = max($datosGraficoCosto);
            $mejorKey   = array_search($mejorMonto, $datosGraficoCosto);
            $peorMonto  = min($valoresConDatos);
            $peorKey    = array_search($peorMonto, $datosGraficoCosto);
        }

        if ($mes === 'todos') {
            $labelsGrafico = array_values($mesesNombre);
            $mejorLabel    = $mejorKey !== '-' ? $mesesNombre[$mejorKey] : '-';
            $peorLabel     = $peorKey !== '-' ? $mesesNombre[$peorKey] : '-';
        } else {
            $labelsGrafico = array_keys($datosGraficoCosto);
            $mejorLabel    = $mejorKey !== '-' ? "Día $mejorKey" : '-';
            $peorLabel     = $peorKey !== '-' ? "Día $peorKey" : '-';
        }

        return view('inventario.index', compact(
            'anio',
            'mes',
            'aniosDisponibles',
            'totalProductos',
            'stockTotal',
            'valorInventario',
            'productosEnCero',
            'cantidadEnCero',
            'topProductos',
            'stockPorMarca',
            'datosGraficoCosto',
            'labelsGrafico',
            'totalEntradasPeriodo',
            'cantidadEntradasPeriodo',
            'mejorLabel',
            'mejorMonto',
            'peorLabel',
            'peorMonto',
            'mesesNombre',
        ));
    }
}
