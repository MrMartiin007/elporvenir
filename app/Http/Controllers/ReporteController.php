<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Pedido;
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

        // Obtener años disponibles para el filtro (de ventas Y pedidos)
        $aniosVentas = Venta::selectRaw('YEAR(fecha_venta) as anio')
            ->distinct()
            ->pluck('anio');

        $aniosPedidos = Pedido::selectRaw('YEAR(created_at) as anio')
            ->distinct()
            ->pluck('anio');

        $aniosDisponibles = $aniosVentas->merge($aniosPedidos)
            ->unique()
            ->sortDesc()
            ->values();

        // ============================================
        // VENTAS EN TIENDA (existente)
        // ============================================
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
            for ($i = 1; $i <= 12; $i++) {
                $datosGrafico[$i] = 0;
            }
        } else {
            $diasEnMes = Carbon::createFromDate($anio, $mes)->daysInMonth;
            for ($i = 1; $i <= $diasEnMes; $i++) {
                $datosGrafico[$i] = 0;
            }
        }

        foreach ($ventas as $venta) {
            $totalVentas += $venta->total_vendido;

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

            $fecha = Carbon::parse($venta->fecha_venta);
            $key = ($mes == 'todos') ? $fecha->month : $fecha->day;

            if (isset($datosGrafico[$key])) {
                $datosGrafico[$key] += $venta->total_vendido;
            }
        }

        $gananciaVentas = $totalVentas - $costoTotal;

        // Estadisticas de Mejor/Peor
        $mejorMonto = -1;
        $mejorKey = '-';
        $peorMonto = -1;
        $peorKey = '-';

        $valoresConVentas = array_filter($datosGrafico, function ($v) {
            return $v > 0;
        });

        if (count($valoresConVentas) > 0) {
            $mejorMonto = max($datosGrafico);
            $mejorKey = array_search($mejorMonto, $datosGrafico);

            $peorMonto = min($valoresConVentas);
            $peorKey = array_search($peorMonto, $datosGrafico);
        }

        // Formatear claves para display
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

        if ($mes == 'todos') {
            $mejorLabel = $mejorKey != '-' ? $mesesNombre[$mejorKey] : '-';
            $peorLabel = $peorKey != '-' ? $mesesNombre[$peorKey] : '-';
            $labelsGrafico = array_values($mesesNombre);
        } else {
            $mejorLabel = $mejorKey != '-' ? "Día $mejorKey" : '-';
            $peorLabel = $peorKey != '-' ? "Día $peorKey" : '-';
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

        // ============================================
        // PEDIDOS ONLINE (nuevo)
        // ============================================
        $estadosValidos = ['confirmado', 'en_proceso', 'enviado', 'entregado'];

        $queryPedidos = Pedido::with(['detalles.producto'])
            ->whereYear('created_at', $anio)
            ->whereIn('estado', $estadosValidos);

        if ($mes != 'todos') {
            $queryPedidos->whereMonth('created_at', $mes);
        }

        $pedidos = $queryPedidos->get();

        $cantidadPedidos = $pedidos->count();
        $totalPedidos = 0; // Se calculará en el bucle


        // Calcular costo y total (revenue) de pedidos online
        $costoTotalPedidos = 0;
        foreach ($pedidos as $pedido) {
            foreach ($pedido->detalles as $detalle) {
                // Cálculo de Costo
                $producto = $detalle->producto;
                $costoUnitario = 0;

                if ($producto && $producto->ultimaEntrada) {
                    $costoUnitario = $producto->ultimaEntrada->precio_costo;
                } elseif ($producto && $producto->entradas->isNotEmpty()) {
                    $costoUnitario = $producto->entradas->last()->precio_costo;
                }
                $costoTotalPedidos += ($detalle->cantidad * $costoUnitario);

                // Cálculo de Venta (Revenue) - manual para asegurar retrocompatibilidad
                $precioReal = $detalle->precio_unitario - ($detalle->descuento ?? 0);
                $totalPedidos += ($detalle->cantidad * $precioReal);
            }
        }

        // Agregar pedidos online como "usuario" en la gráfica de rendimiento
        if ($totalPedidos > 0) {
            $ventasPorUsuario['Pedidos Online 🛒'] = $totalPedidos;
        }

        // Sumar pedidos al total general de ventas y ganancia
        $totalVentas += $totalPedidos;
        $costoTotal += $costoTotalPedidos;
        $gananciaTotal = $gananciaVentas + ($totalPedidos - $costoTotalPedidos);

        // Datos para gráfico de pedidos (misma estructura que ventas)
        $datosPedidosGrafico = [];
        if ($mes == 'todos') {
            for ($i = 1; $i <= 12; $i++) {
                $datosPedidosGrafico[$i] = 0;
            }
        } else {
            $diasEnMes = Carbon::createFromDate($anio, $mes)->daysInMonth;
            for ($i = 1; $i <= $diasEnMes; $i++) {
                $datosPedidosGrafico[$i] = 0;
            }
        }

        foreach ($pedidos as $pedido) {
            $fecha = Carbon::parse($pedido->created_at);
            $key = ($mes == 'todos') ? $fecha->month : $fecha->day;

            if (isset($datosPedidosGrafico[$key])) {
                // Calcular subtotal manual para el gráfico también
                $subtotalPedido = $pedido->detalles->sum(function ($d) {
                    return ($d->precio_unitario - ($d->descuento ?? 0)) * $d->cantidad;
                });
                $datosPedidosGrafico[$key] += $subtotalPedido;
            }
        }

        // Conteo de pedidos por estado (todos los estados, sin filtro)
        $queryPedidosEstado = Pedido::whereYear('created_at', $anio);
        if ($mes != 'todos') {
            $queryPedidosEstado->whereMonth('created_at', $mes);
        }

        $pedidosPorEstado = $queryPedidosEstado
            ->selectRaw('estado, COUNT(*) as total, SUM(total - IFNULL(envio, 0)) as monto')
            ->groupBy('estado')
            ->get()
            ->keyBy('estado');

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
            'ventasPorUsuario',
            'datosPedidosGrafico',
            'pedidosPorEstado'
        ));
    }
}

