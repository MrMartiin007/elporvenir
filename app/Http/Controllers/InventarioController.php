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
        $anio = (int) $request->input('anio', Carbon::now()->year);
        $mes  = $request->input('mes', 'todos');

        // ─── Años disponibles (basado en entradas) ───────────────────────────
        $aniosDisponibles = Entrada::selectRaw('YEAR(fecha_ingreso) as anio')
            ->distinct()
            ->orderByDesc('anio')
            ->pluck('anio')
            ->filter()
            ->values();

        if ($aniosDisponibles->isEmpty()) {
            $aniosDisponibles = collect([Carbon::now()->year]);
        }
        
        // Si el año consultado no está, incluirlo de todas formas en el dropdown
        if (!$aniosDisponibles->contains($anio)) {
            $aniosDisponibles->push($anio);
            $aniosDisponibles = $aniosDisponibles->sortDesc()->values();
        }

        // ─── Cargar todos los productos con relaciones ───────────────────────
        $todosLosProductos = Producto::with(['ultimaEntrada', 'marca'])->get();

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

        // ─── Gráfica de Valor Histórico (Ingeniería Inversa) ────────────────
        // Calculamos el valor del inventario en el tiempo revirtiendo los movimientos
        // desde el stock actual exacto. 
        $now = Carbon::now();
        $startOfChart = ($mes === 'todos') 
            ? Carbon::create($anio, 1, 1)->startOfDay()
            : Carbon::create($anio, $mes, 1)->startOfDay();

        // Limitar la fecha de inicio al momento actual si la consulta es futura
        if ($startOfChart > $now) {
            $startOfChart = clone $now; 
        }

        $entradasFuturas = Entrada::where('fecha_ingreso', '>=', $startOfChart)
            ->where('fecha_ingreso', '<=', $now)
            ->get(['productos_id', 'cantidad', 'fecha_ingreso']);

        $ventasFuturas = DB::table('detalle_ventas')
            ->join('ventas', 'detalle_ventas.ventas_id', '=', 'ventas.id')
            ->where('ventas.fecha_venta', '>=', $startOfChart)
            ->where('ventas.fecha_venta', '<=', $now)
            ->select('detalle_ventas.productos_id', 'detalle_ventas.cantidad', 'ventas.fecha_venta')
            ->get();

        // Valor base (antes de aplicar los movimientos del período en la gráfica)
        $valorAcumulado = $valorInventario;

        foreach ($entradasFuturas as $e) {
            $costo = $costoPorProducto[$e->productos_id] ?? 0;
            // Para ir al pasado, restamos lo que entró después
            $valorAcumulado -= ((int) $e->cantidad * $costo); 
        }

        foreach ($ventasFuturas as $v) {
            $costo = $costoPorProducto[$v->productos_id] ?? 0;
            // Para ir al pasado, sumamos lo que salió después
            $valorAcumulado += ((int) $v->cantidad * $costo); 
        }

        // Construir la gráfica y sumar mes a mes (o día a día)
        $datosGraficoCosto = [];

        if ($mes === 'todos') {
            for ($m = 1; $m <= 12; $m++) {
                // Movimientos de ESTE mes
                $entradasMes = $entradasFuturas->filter(function($e) use ($m, $anio) {
                    $d = Carbon::parse($e->fecha_ingreso);
                    return $d->month == $m && $d->year == $anio;
                });
                
                $ventasMes = $ventasFuturas->filter(function($v) use ($m, $anio) {
                    $d = Carbon::parse($v->fecha_venta);
                    return $d->month == $m && $d->year == $anio;
                });

                // Avanzamos hacia adelante de nuevo, mes a mes
                foreach ($entradasMes as $e) {
                    $costo = $costoPorProducto[$e->productos_id] ?? 0;
                    $valorAcumulado += ((int) $e->cantidad * $costo); // Entró -> Sube el stock
                }
                foreach ($ventasMes as $v) {
                    $costo = $costoPorProducto[$v->productos_id] ?? 0;
                    $valorAcumulado -= ((int) $v->cantidad * $costo); // Salió -> Baja el stock
                }

                $datosGraficoCosto[$m] = $valorAcumulado;
            }
        } else {
            $diasEnMes = Carbon::createFromDate($anio, $mes)->daysInMonth;
            for ($d = 1; $d <= $diasEnMes; $d++) {
                // Movimientos de ESTE día
                $entradasDia = $entradasFuturas->filter(function($e) use ($d, $mes, $anio) {
                    $dt = Carbon::parse($e->fecha_ingreso);
                    return $dt->day == $d && $dt->month == $mes && $dt->year == $anio;
                });
                
                $ventasDia = $ventasFuturas->filter(function($v) use ($d, $mes, $anio) {
                    $dt = Carbon::parse($v->fecha_venta);
                    return $dt->day == $d && $dt->month == $mes && $dt->year == $anio;
                });

                // Avanzamos hacia adelante de nuevo, día a día
                foreach ($entradasDia as $e) {
                    $costo = $costoPorProducto[$e->productos_id] ?? 0;
                    $valorAcumulado += ((int) $e->cantidad * $costo);
                }
                foreach ($ventasDia as $v) {
                    $costo = $costoPorProducto[$v->productos_id] ?? 0;
                    $valorAcumulado -= ((int) $v->cantidad * $costo);
                }

                $datosGraficoCosto[$d] = $valorAcumulado;
            }
        }

        $mesesNombre = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo',  6 => 'Junio',   7 => 'Julio',  8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        if ($mes === 'todos') {
            $labelsGrafico = array_values($mesesNombre);
        } else {
            $labelsGrafico = array_map(fn($d) => (string)$d, array_keys($datosGraficoCosto));
        }

        return view('inventario.index', compact(
            'anio',
            'mes',
            'aniosDisponibles',
            'mesesNombre',
            'totalProductos',
            'stockTotal',
            'valorInventario',
            'productosEnCero',
            'cantidadEnCero',
            'topProductos',
            'datosGraficoCosto',
            'labelsGrafico'
        ));
    }
}
