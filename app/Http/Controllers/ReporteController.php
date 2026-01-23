<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Entrada;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $anio = $request->input('anio', Carbon::now()->year);
        $mes = $request->input('mes', 'todos'); // 'todos' o 1-12

        // Obtener años disponibles para el filtro
        $aniosDisponibles = Venta::selectRaw('YEAR(fecha_venta) as anio')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        // Construir consulta base
        $query = Venta::with(['detalles.producto'])
            ->whereYear('fecha_venta', $anio)
            ->where('estado', '0'); // 0 = Venta Cerrada/Finalizada

        if ($mes != 'todos') {
            $query->whereMonth('fecha_venta', $mes);
        }

        $ventas = $query->get();

        $totalVentas = 0;
        $costoTotal = 0;
        $datosGrafico = [];

        // Inicializar estructura de datos para grafico
        if ($mes == 'todos') {
            // Vista Anual: Inicializar 12 meses
            for ($i = 1; $i <= 12; $i++) {
                $datosGrafico[$i] = 0;
            }
        } else {
            // Vista Mensual: Inicializar dias del mes
            $diasEnMes = Carbon::createFromDate($anio, $mes)->daysInMonth;
            for ($i = 1; $i <= $diasEnMes; $i++) {
                $datosGrafico[$i] = 0;
            }
        }

        foreach ($ventas as $venta) {
            $totalVentas += $venta->total_vendido;

            // Calculo de costo
            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;
                $costoUnitario = 0;

                if ($producto && $producto->ultimaEntrada) {
                    $costoUnitario = $producto->ultimaEntrada->precio_costo;
                } elseif ($producto && $producto->entradas->isNotEmpty()) {
                    $costoUnitario = $producto->entradas->last()->precio_costo;
                }
                $costoTotal += ($detalle->cantidad * $costoUnitario);
            }

            // Agrupacion para grafico
            $fecha = Carbon::parse($venta->fecha_venta);
            if ($mes == 'todos') {
                $key = $fecha->month;
            } else {
                $key = $fecha->day;
            }

            if (isset($datosGrafico[$key])) {
                $datosGrafico[$key] += $venta->total_vendido;
            }
        }

        $gananciaTotal = $totalVentas - $costoTotal;

        // Estadisticas de Mejor/Peor
        $mejorMonto = -1;
        $mejorKey = '-';
        $peorMonto = -1;
        $peorKey = '-';

        // Filtrar solo las claves que tienen ventas para "Peor" (opcional: o incluir ceros si se quiere)
        // Para "Peor" dia/mes con ventas, filtramos los 0 si queremos dias ACTIVOS, 
        // pero el usuario pidio "dia menos vendido", que podria implicar dias con ventas bajas, no necesariamente 0.
        // Vamos a considerar dias con venta > 0 para "peor venta", o el minimo general.
        // Si queremos el dia REALMENTE malo (0 ventas), seria el min de todos. 
        // Asumamos que busca el dia con ventas mas bajas pero que SI hubo ventas, o 0.

        // Vamos a separar los valores > 0 para encontrar el minimo activo.
        $valoresConVentas = array_filter($datosGrafico, function ($v) {
            return $v > 0;
        });

        if (count($valoresConVentas) > 0) {
            $mejorMonto = max($datosGrafico);
            $mejorKey = array_search($mejorMonto, $datosGrafico);

            $peorMonto = min($valoresConVentas);
            $peorKey = array_search($peorMonto, $datosGrafico); // Esto buscara el primer dia con ese monto
        }

        // Formatear claves para display
        if ($mes == 'todos') {
            $mesesNombre = [
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre'
            ];
            $mejorLabel = $mejorKey != '-' ? $mesesNombre[$mejorKey] : '-';
            $peorLabel = $peorKey != '-' ? $mesesNombre[$peorKey] : '-';
            $labelsGrafico = array_values($mesesNombre);
        } else {
            $mejorLabel = $mejorKey != '-' ? "Día $mejorKey" : '-';
            $peorLabel = $peorKey != '-' ? "Día $peorKey" : '-';
            // Labels para dias 1..N
            $labelsGrafico = array_keys($datosGrafico);
        }

        // Calcular ventas por usuario
        $ventasPorUsuario = [];
        foreach ($ventas as $venta) {
            $nombreUsuario = $venta->user ? $venta->user->name : 'Desconocido';
            if (!isset($ventasPorUsuario[$nombreUsuario])) {
                $ventasPorUsuario[$nombreUsuario] = 0;
            }
            $ventasPorUsuario[$nombreUsuario] += $venta->total_vendido;
        }

        return view('reporte.index', compact(
            'totalVentas',
            'costoTotal',
            'gananciaTotal',
            'anio',
            'mes',
            'aniosDisponibles',
            'datosGrafico',
            'labelsGrafico',
            'mejorLabel',
            'mejorMonto',
            'peorLabel',
            'peorMonto',
            'ventasPorUsuario'
        ));
    }
}
