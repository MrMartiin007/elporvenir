<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    /**
     * Mostrar formulario de checkout
     */
    public function index()
    {
        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }

        // Calcular totales
        $subtotal = 0;
        foreach ($carrito as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
        }
        // Obtener tarifa de envío dinámica
        $tarifa = \App\Models\TarifaEnvio::where('activo', true)->latest()->first();
        $envio = $tarifa ? $tarifa->costo : 35.00;

        $total = $subtotal + $envio;

        // Obtener departamentos activos
        $departamentos = Departamento::activos()->orderBy('nombre')->get();

        return view('checkout.index', compact('carrito', 'subtotal', 'envio', 'total', 'departamentos'));
    }

    /**
     * Procesar el pedido
     */
    public function procesar(Request $request)
    {
        // Validar reCAPTCHA v3
        $recaptchaToken = $request->input('g-recaptcha-response');
        if (!$this->validateRecaptcha($recaptchaToken)) {
            return back()->withInput()->with('error', 'Verificación de seguridad fallida. Por favor intenta de nuevo.');
        }

        // Validar datos del cliente
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'departamento_id' => 'required|exists:departamentos,id',
            'municipio_id' => 'required|exists:municipios,id',
            'direccion' => 'required|string|max:500',
            'notas' => 'nullable|string|max:1000'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'telefono.required' => 'El teléfono es obligatorio',
            'departamento_id.required' => 'El departamento es obligatorio',
            'municipio_id.required' => 'El municipio es obligatorio',
            'direccion.required' => 'La dirección de entrega es obligatoria'
        ]);

        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }

        // Iniciar transacción
        DB::beginTransaction();

        try {
            // Calcular totales
            $subtotal = 0;
            $cantidadTotal = 0;

            foreach ($carrito as $item) {
                $subtotal += $item['precio'] * $item['cantidad'];
                $cantidadTotal += $item['cantidad'];
            }

            // Obtener tarifa de envío dinámica
            $tarifa = \App\Models\TarifaEnvio::where('activo', true)->latest()->first();
            $envio = $tarifa ? $tarifa->costo : 35.00;

            $total = $subtotal + $envio;

            // Crear pedido
            $pedido = Pedido::create([
                'nombre_cliente' => $validated['nombre'],
                'telefono_cliente' => $validated['telefono'],
                'email_cliente' => $validated['email'] ?? '',
                'departamento_id' => $validated['departamento_id'],
                'municipio_id' => $validated['municipio_id'],
                'direccion_cliente' => $validated['direccion'],
                'notas_cliente' => $validated['notas'] ?? '',
                'subtotal' => $subtotal,
                'descuento' => 0,
                'envio' => $envio, // Guardar el costo de envío actual
                'total' => $total,
                'cantidad_productos' => $cantidadTotal,
                'estado' => 'pendiente',
                'numero_pedido' => Pedido::generarNumeroPedido()
            ]);

            // Crear detalles del pedido (sin descontar stock)
            foreach ($carrito as $productoId => $item) {
                DetallePedido::create([
                    'pedidos_id' => $pedido->id,
                    'productos_id' => $productoId,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'descuento' => 0,
                    'subtotal' => $item['precio'] * $item['cantidad']
                ]);
            }

            // Commit de la transacción
            DB::commit();

            // Limpiar carrito
            session()->forget('carrito');

            // Redirigir a confirmación
            return redirect()->route('cart.checkout.confirmacion', $pedido->id)
                ->with('success', '¡Pedido realizado con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar confirmación del pedido
     */
    public function confirmacion($id)
    {
        $pedido = Pedido::with('detalles.producto', 'departamento', 'municipio')->findOrFail($id);
        return view('checkout.confirmacion', compact('pedido'));
    }

    /**
     * Obtener municipios por departamento (para select dinámico)
     */
    public function getMunicipios($departamento_id)
    {
        $municipios = \App\Models\Municipio::where('departamento_id', $departamento_id)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($municipios);
    }

    /**
     * Validar token de reCAPTCHA v3
     */
    private function validateRecaptcha(?string $token): bool
    {
        $secretKey = config('services.recaptcha.secret_key');

        // Si no hay secret key configurada, saltar validación (dev)
        if (empty($secretKey)) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            // Verificar que sea exitoso y que el score sea >= 0.5
            return ($result['success'] ?? false) && ($result['score'] ?? 0) >= 0.5;
        } catch (\Exception $e) {
            // Si falla la verificación, permitir pasar (fail-open para no bloquear clientes)
            return true;
        }
    }
}
