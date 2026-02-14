<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\DatosEnvio;
use App\Models\DetallePedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Mostrar lista de pedidos
     */
    public function index(Request $request)
    {
        $query = Pedido::query();

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Búsqueda por nombre o número de pedido
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_pedido', 'like', "%$search%")
                    ->orWhere('nombre_cliente', 'like', "%$search%");
            });
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Mostrar detalles del pedido
     */
    public function show($id)
    {
        $pedido = Pedido::with('detalles.producto', 'departamento', 'municipio')->findOrFail($id);
        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Actualizar estado del pedido
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,enviado,entregado,cancelado'
        ]);

        $pedido = Pedido::with('detalles')->findOrFail($id);
        $nuevoEstado = $request->estado;
        $estadoAnterior = $pedido->estado;

        // Si el estado no cambia, retornar
        if ($nuevoEstado === $estadoAnterior) {
            return back()->with('info', 'El pedido ya tiene ese estado.');
        }

        DB::beginTransaction();
        try {
            // Lógica de Stock

            // 1. Si pasamos a CONFIRMADO (desde pendiente o cancelado) -> Descontar Stock
            if ($nuevoEstado === 'confirmado' && ($estadoAnterior === 'pendiente' || $estadoAnterior === 'cancelado')) {
                foreach ($pedido->detalles as $detalle) {
                    $producto = Producto::find($detalle->productos_id);
                    if ($producto) {
                        // Solo descontar si hay stock disponible, nunca dejar en negativo
                        if ($producto->stock > 0) {
                            $descontar = min($producto->stock, $detalle->cantidad);
                            $producto->decrement('stock', $descontar);
                        }
                    }
                }
            }

            // 2. Si pasamos a CANCELADO (desde confirmado, enviado o entregado) -> Devolver Stock
            // Nota: Si estaba pendiente, nunca se descontó, así que no se devuelve.
            if ($nuevoEstado === 'cancelado' && in_array($estadoAnterior, ['confirmado', 'enviado'])) {
                foreach ($pedido->detalles as $detalle) {
                    $producto = Producto::find($detalle->productos_id);
                    if ($producto) {
                        $producto->increment('stock', $detalle->cantidad);
                    }
                }
            }

            // 3. Casos especiales:
            // De Entregado a Cancelado? Raro, pero si pasa, devolvemos stock.
            if ($nuevoEstado === 'cancelado' && $estadoAnterior === 'entregado') {
                foreach ($pedido->detalles as $detalle) {
                    $producto = Producto::find($detalle->productos_id);
                    if ($producto) {
                        $producto->increment('stock', $detalle->cantidad);
                    }
                }
            }

            // Actualizar estado
            $pedido->estado = $nuevoEstado;
            $pedido->save();

            DB::commit();

            return back()->with('success', "Estado actualizado a " . ucfirst($nuevoEstado));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Buscar productos para agregar al pedido (AJAX)
     */
    public function searchProducts(Request $request)
    {
        $term = $request->get('term');

        Log::info("Buscando producto: " . $term);

        $query = Producto::query();

        if (!empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->where('codigo_producto', 'like', "%$term%")
                    ->orWhere('detalle_producto', 'like', "%$term%");
            });
        }

        $productos = $query->take(20)->get();

        // Formatear para respuesta JSON incluyendo precio y stock
        $results = $productos->map(function ($prod) {
            return [
                'id' => $prod->id,
                'codigo' => $prod->codigo_producto,
                'nombre' => $prod->detalle_producto,
                'stock' => $prod->stock,
                'precio' => $prod->ultimaEntrada ? $prod->ultimaEntrada->precio_venta : 0,
                'imagen' => $prod->foto_producto ? asset('storage/' . $prod->foto_producto) : null
            ];
        });

        return response()->json($results);
    }

    /**
     * Agregar producto al pedido
     */
    public function addDetail(Request $request, $pedidoId)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'nullable|integer|min:1'
        ]);

        $pedido = Pedido::findOrFail($pedidoId);
        $producto = Producto::with('ultimaEntrada')->findOrFail($request->producto_id);

        $cantidad = $request->input('cantidad', 1);
        $precio = $producto->ultimaEntrada ? $producto->ultimaEntrada->precio_venta : 0;

        if ($precio <= 0) {
            return back()->with('error', 'El producto no tiene precio venta registrado.');
        }

        DB::beginTransaction();
        try {
            // Verificar si el producto ya existe en el pedido para agruparlo
            $detalleExistente = DetallePedido::where('pedidos_id', $pedido->id)
                ->where('productos_id', $producto->id)
                ->first();

            if ($detalleExistente) {
                $detalleExistente->cantidad += $cantidad;
                $detalleExistente->subtotal = $detalleExistente->cantidad * $detalleExistente->precio_unitario;
                $detalleExistente->save();
            } else {
                DetallePedido::create([
                    'pedidos_id' => $pedido->id,
                    'productos_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $precio * $cantidad
                ]);
            }

            // Si el pedido está CONFIRMADO, descontar stock (Permisivo)
            if (in_array($pedido->estado, ['confirmado', 'enviado', 'entregado'])) {
                if ($producto->stock > 0) {
                    $descontar = min($producto->stock, $cantidad);
                    $producto->decrement('stock', $descontar);
                }
            }

            // Recalcular totales del pedido
            $this->recalculateTotals($pedido);

            DB::commit();
            return back()->with('success', 'Producto agregado al pedido.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al agregar producto: ' . $e->getMessage());
        }
    }

    /**
     * Marcar pedido como ENVIADO (con guía y comprobante)
     */
    public function markAsShipped(Request $request, $id)
    {
        $request->validate([
            'numero_guia' => 'required|string|max:255',
            'comprobante' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $pedido = Pedido::findOrFail($id);

        if ($pedido->estado == 'cancelado') {
            return back()->with('error', 'No se puede enviar un pedido cancelado.');
        }

        DB::beginTransaction();
        try {
            // Guardar archivo
            if ($request->hasFile('comprobante')) {
                $file = $request->file('comprobante');
                $path = $file->store('comprobantes', 'public');
                $tipo = $file->getClientOriginalExtension() == 'pdf' ? 'pdf' : 'img';

                DatosEnvio::create([
                    'pedidos_id' => $pedido->id,
                    'numero_guia' => $request->numero_guia,
                    'comprobante' => $path,
                    'tipo_comprobante' => $tipo
                ]);
            }

            // Cambiar estado
            // Si no estaba confirmado, descontar stock ahora (aunque debería estar confirmado antes de enviar)
            if ($pedido->estado == 'pendiente') {
                foreach ($pedido->detalles as $detalle) {
                    if ($detalle->producto && $detalle->producto->stock > 0) {
                        $descontar = min($detalle->producto->stock, $detalle->cantidad);
                        $detalle->producto->decrement('stock', $descontar);
                    }
                }
            }

            $pedido->estado = 'enviado';
            $pedido->save();

            DB::commit();
            return back()->with('success', 'Pedido marcado como ENVIADO correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar envío: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar detalle del pedido (Cantidad)
     */
    public function updateDetail(Request $request, $pedidoId, $detalleId)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        $detalle = DetallePedido::findOrFail($detalleId);
        $pedido = Pedido::findOrFail($pedidoId); // Aseguramos que existe

        // Verificar que el detalle pertenezca al pedido especificado en la ruta (seguridad)
        if ($detalle->pedidos_id != $pedido->id) {
            return back()->with('error', 'El detalle no pertenece al pedido especificado.');
        }

        $nuevaCantidad = (int) $request->cantidad;
        $cantidadAnterior = (int) $detalle->cantidad;

        if ($nuevaCantidad === $cantidadAnterior) {
            return back();
        }

        DB::beginTransaction();
        try {
            // Si el pedido ya estaba CONFIRMADO (o en proceso), ajustar stock
            if (in_array($pedido->estado, ['confirmado', 'enviado', 'entregado'])) {
                $producto = Producto::find($detalle->productos_id);

                if ($producto) {
                    $diferencia = $nuevaCantidad - $cantidadAnterior;

                    if ($diferencia > 0) {
                        // Aumentó la cantidad: Intentar descontar stock
                        if ($producto->stock > 0) {
                            $descontar = min($producto->stock, $diferencia);
                            $producto->decrement('stock', $descontar);
                        }
                    } elseif ($diferencia < 0) {
                        // Disminuyó la cantidad: Devolver stock siempre
                        $producto->increment('stock', abs($diferencia));
                    }
                }
            }

            // Actualizar detalle
            $detalle->cantidad = $nuevaCantidad;
            $detalle->subtotal = $detalle->precio_unitario * $nuevaCantidad; // Recalcular subtotal línea
            $detalle->save();

            // Recalcular totales del pedido
            $this->recalculateTotals($pedido);

            DB::commit();
            return back()->with('success', 'Cantidad actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar cantidad: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar detalle del pedido
     */
    public function destroyDetail($pedidoId, $detalleId)
    {
        $detalle = DetallePedido::findOrFail($detalleId);
        $pedido = Pedido::findOrFail($pedidoId);

        if ($detalle->pedidos_id != $pedido->id) {
            return back()->with('error', 'El detalle no pertenece al pedido.');
        }

        DB::beginTransaction();
        try {
            // Si el pedido ya estaba CONFIRMADO, devolver stock
            if (in_array($pedido->estado, ['confirmado', 'enviado', 'entregado'])) {
                $producto = Producto::find($detalle->productos_id);
                if ($producto) {
                    $producto->increment('stock', $detalle->cantidad);
                }
            }

            $detalle->delete();

            // Recalcular totales
            $this->recalculateTotals($pedido);

            DB::commit();
            return back()->with('success', 'Producto eliminado del pedido.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar producto: ' . $e->getMessage());
        }
    }

    /**
     * Recalcular totales del pedido (Subtotal, Envío, Total)
     */
    private function recalculateTotals(Pedido $pedido)
    {
        // 1. Calcular Subtotal
        $pedido->subtotal = $pedido->detalles()->sum('subtotal');

        // 2. Obtener Costo de Envío
        // Usamos la última tarifa activa, o mantenemos la que tiene si no hay tarifa, o 35 por defecto
        $tarifa = \App\Models\TarifaEnvio::where('activo', true)->latest()->first();
        $costoEnvio = $tarifa ? $tarifa->costo : 35.00; // Default 35 si no hay config

        // Actualizamos el envío del pedido al valor actual
        $pedido->envio = $costoEnvio;

        // 3. Calcular Total
        if ($pedido->subtotal > 0) {
            $pedido->total = $pedido->subtotal + $pedido->envio;
        } else {
            $pedido->total = 0; // Si no hay productos, total 0 (opcional, o cobrar solo envío?)
        }

        $pedido->save();
    }
}
