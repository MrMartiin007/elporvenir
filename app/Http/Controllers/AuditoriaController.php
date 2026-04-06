<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\DetalleAuditoria;
use App\Models\Entrada;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditoriaController extends Controller
{
    /**
     * Muestra el historial de auditorías.
     */
    public function index()
    {
        // Buscar si hay una auditoría activa
        $auditoriaActiva = Auditoria::where('users_id', auth()->id())
            ->where('estado', 1)
            ->first();

        if ($auditoriaActiva) {
            return redirect()->route('auditoria.create');
        }

        // Mostrar el historial de auditorías
        if (auth()->user()->hasRole('superadmin')) {
            $auditorias = Auditoria::with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $auditorias = Auditoria::where('users_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('auditoria.index', compact('auditorias'));
    }

    /**
     * Inicia una nueva auditoría diaria/sesión.
     */
    public function iniciarAuditoria()
    {
        $auditoria = Auditoria::create([
            'fecha_auditoria' => now(),
            'cantidad_productos' => 0,
            'total_auditado' => 0,
            'estado' => 1,
            'users_id' => auth()->id(),
        ]);

        return redirect()->route('auditoria.create');
    }

    /**
     * Muestra la interfaz de escáner y la lista de productos auditados en la sesión actual.
     */
    public function create(Request $request)
    {
        $productosFiltrados = collect();
        $ultimaEntrada = null;

        $auditoria = Auditoria::with(['detalles' => function($query) {
            $query->orderBy('id', 'desc');
        }, 'detalles.producto', 'detalles.user'])
            ->where('users_id', auth()->id())
            ->where('estado', 1)
            ->first();

        if (!$auditoria) {
            return redirect()->route('auditoria.index')
                ->with('error', 'No hay una auditoría activa. Inicia una para comenzar.');
        }

        if ($request->filled('buscar')) {
            $productosFiltrados = Producto::with(['marca', 'ultimaEntrada'])
                ->where('codigo_producto', $request->buscar)
                ->get();

            if ($productosFiltrados->count() === 1) {
                $producto = $productosFiltrados->first();
                $ultimaEntrada = Entrada::where('productos_id', $producto->id)
                    ->latest()
                    ->first();
            }

            // Si no se encontró el producto, redirige a productos.create
            if ($productosFiltrados->isEmpty()) {
                return redirect()->route('productos.create')->with([
                    'bad_status' => 'El producto con código "' . $request->buscar . '" no fue encontrado. Por favor, regístralo.',
                ]);
            }
        }

        return view('auditoria.create', compact('auditoria', 'productosFiltrados', 'ultimaEntrada'));
    }

    /**
     * Guarda la corrección de auditoría.
     */
    public function store(Request $request)
    {
        $auditoria = Auditoria::where('users_id', auth()->id())
            ->where('estado', 1)
            ->first();

        if (!$auditoria) {
            return redirect()->route('auditoria.index')
                ->with('error', 'No hay una auditoría activa.');
        }

        $request->validate([
            'productos_id'  => 'required|exists:productos,id',
            'stock_nuevo'   => 'required|integer|min:0',
            'precio_costo'  => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'precio_docena' => 'required|numeric|min:0',
        ]);

        $producto = Producto::findOrFail($request->productos_id);
        $ultimaEntrada = Entrada::where('productos_id', $producto->id)->latest()->first();

        // Guardar valores anteriores para el log
        $stockAnterior        = $producto->stock;
        $costoanterior        = $ultimaEntrada?->precio_costo ?? 0;
        $ventaAnterior        = $ultimaEntrada?->precio_venta ?? 0;
        $docenaAnterior       = $ultimaEntrada?->precio_docena ?? 0;

        // 1. Actualizar stock del producto
        $producto->stock = $request->stock_nuevo;
        $producto->save();

        // 2. Actualizar precios de la última entrada (si existe)
        if ($ultimaEntrada) {
            $ultimaEntrada->update([
                'precio_costo'  => $request->precio_costo,
                'precio_venta'  => $request->precio_venta,
                'precio_docena' => $request->precio_docena,
            ]);
        }

        // 3. Registrar en detalle_auditorias
        // Si el mismo producto ya fue escaneado en esta sesión, actualizamos el registro
        // existente en vez de crear uno nuevo (evita duplicados y sumas incorrectas).
        $detalleExistente = DetalleAuditoria::where('auditorias_id', $auditoria->id)
            ->where('productos_id', $producto->id)
            ->first();

        if ($detalleExistente) {
            // Conservamos el stock_anterior original (del primer escaneo) para mantener trazabilidad
            $detalleExistente->update([
                'user_id'                => Auth::id(),
                'stock_nuevo'            => $request->stock_nuevo,
                'precio_costo_anterior'  => $costoanterior,
                'precio_costo_nuevo'     => $request->precio_costo,
                'precio_venta_anterior'  => $ventaAnterior,
                'precio_venta_nuevo'     => $request->precio_venta,
                'precio_docena_anterior' => $docenaAnterior,
                'precio_docena_nuevo'    => $request->precio_docena,
            ]);
        } else {
            DetalleAuditoria::create([
                'auditorias_id'          => $auditoria->id,
                'productos_id'           => $producto->id,
                'user_id'                => Auth::id(),
                'stock_anterior'         => $stockAnterior,
                'stock_nuevo'            => $request->stock_nuevo,
                'precio_costo_anterior'  => $costoanterior,
                'precio_costo_nuevo'     => $request->precio_costo,
                'precio_venta_anterior'  => $ventaAnterior,
                'precio_venta_nuevo'     => $request->precio_venta,
                'precio_docena_anterior' => $docenaAnterior,
                'precio_docena_nuevo'    => $request->precio_docena,
            ]);
        }

        // 4. Actualizar totales en la sesión de auditoría
        $auditoria->cantidad_productos = $auditoria->detalles()->count();
        // total sum of (stock_nuevo * precio_costo_nuevo) of all details
        $auditoria->total_auditado = $auditoria->detalles()->sum(\DB::raw('stock_nuevo * precio_costo_nuevo'));
        $auditoria->save();

        return redirect()->route('auditoria.create')
            ->with('success', '✅ Auditoría guardada: ' . $producto->detalle_producto);
    }

    /**
     * Cierra la auditoría activa.
     */
    public function cerrarAuditoria(Auditoria $auditoria)
    {
        $auditoria->estado = 0;
        $auditoria->save();

        return redirect()->route('auditoria.index')
            ->with('success', 'La auditoría ha sido cerrada correctamente.');
    }

    /**
     * Muestra el resumen de una auditoría cerrada.
     */
    public function show($id)
    {
        $auditoria = Auditoria::with(['detalles' => function($query) {
            $query->orderBy('id', 'desc');
        }, 'detalles.producto', 'user'])->findOrFail($id);
        
        return view('auditoria.show', compact('auditoria'));
    }
}
