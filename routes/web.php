<?php

use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VentaController;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [App\Http\Controllers\ShopController::class, 'shop'])->name('home');

// Rutas Legacy (antiguas) para SEO (Google Search Console 404s)
Route::get('/producto/{id}', function ($id) {
    if (is_numeric($id)) {
        $producto = \App\Models\Producto::find($id);
        if ($producto) {
            $hash = \App\Helpers\IdObfuscator::encode($producto->id);
            $newSlug = \Illuminate\Support\Str::slug($producto->detalle_producto);
            return redirect()->route('producto.show', ['hash' => $hash, 'slug' => $newSlug], 301);
        }
    }
    abort(404);
})->where('id', '[0-9]+');

Route::get('/producto/{hash}-{slug?}', [App\Http\Controllers\ShopController::class, 'showProduct'])
    ->name('producto.show');
Route::get('/contacto', [App\Http\Controllers\ShopController::class, 'contact'])->name('contact');

// Rutas del carrito/shop (públicas - no requieren autenticación)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CarritoController::class, 'index'])->name('index');
    Route::post('/agregar', [CarritoController::class, 'agregar'])->name('agregar');
    Route::patch('/actualizar', [CarritoController::class, 'actualizar'])->name('actualizar');
    Route::delete('/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('eliminar');
    Route::delete('/vaciar', [CarritoController::class, 'vaciar'])->name('vaciar');

    // Rutas del checkout (dentro de shop)
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/procesar', [CheckoutController::class, 'procesar'])->name('procesar');
        Route::get('/municipios/{departamento_id}', [CheckoutController::class, 'getMunicipios'])->name('municipios');
        Route::get('/confirmacion/{id}', [CheckoutController::class, 'confirmacion'])->name('confirmacion');
    });
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::middleware(['auth', 'role:superadmin|venta'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::resource('productos', ProductoController::class);
        Route::resource('marcas', MarcaController::class);
        Route::resource('entradas', EntradaController::class);
        Route::resource('ventas', VentaController::class);
        Route::resource('detalle-ventas', DetalleVenta::class);
        Route::get('/producto/buscar', [VentaController::class, 'buscarProducto'])->name('productos.buscar');
        Route::get('/producto/consultar', [ProductoController::class, 'consultarProducto'])->name('productos.consultar');

        // Ubicaciones (Departamentos y Municipios)
        Route::get('/ubicaciones', [App\Http\Controllers\UbicacionController::class, 'index'])->name('ubicaciones.index');
        Route::post('/ubicaciones/departamento', [App\Http\Controllers\UbicacionController::class, 'storeDepartamento'])->name('ubicaciones.departamento.store');
        Route::put('/ubicaciones/departamento/{id}', [App\Http\Controllers\UbicacionController::class, 'updateDepartamento'])->name('ubicaciones.departamento.update');
        Route::delete('/ubicaciones/departamento/{id}', [App\Http\Controllers\UbicacionController::class, 'destroyDepartamento'])->name('ubicaciones.departamento.destroy');
        Route::post('/ubicaciones/municipio', [App\Http\Controllers\UbicacionController::class, 'storeMunicipio'])->name('ubicaciones.municipio.store');
        Route::put('/ubicaciones/municipio/{id}', [App\Http\Controllers\UbicacionController::class, 'updateMunicipio'])->name('ubicaciones.municipio.update');
        Route::delete('/ubicaciones/municipio/{id}', [App\Http\Controllers\UbicacionController::class, 'destroyMunicipio'])->name('ubicaciones.municipio.destroy');
        Route::patch('/ubicaciones/toggle/{tipo}/{id}', [App\Http\Controllers\UbicacionController::class, 'toggleActivo'])->name('ubicaciones.toggle');
    });
});

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::resource('empresas', App\Http\Controllers\EmpresaController::class);
        Route::resource('facturas', App\Http\Controllers\FacturaController::class);

        // Reportes Module
        Route::resource('reportes', App\Http\Controllers\ReporteController::class)->only(['index']);

        // Inventario Module
        Route::get('/inventario', [App\Http\Controllers\InventarioController::class, 'index'])->name('inventario.index');

        // Calendar Routes
        Route::get('/calendario', [App\Http\Controllers\CalendarioController::class, 'index'])->name('calendario.index');
        Route::patch('/cheques/{id}/confirmar', [App\Http\Controllers\CalendarioController::class, 'confirmarCheque'])->name('cheques.confirmar');
        Route::patch('/tarjetas/{id}/confirmar', [App\Http\Controllers\CalendarioController::class, 'confirmarTarjeta'])->name('tarjetas.confirmar');
        Route::patch('/cheques/{id}/anular', [App\Http\Controllers\CalendarioController::class, 'anularCheque'])->name('cheques.anular');
        Route::patch('/tarjetas/{id}/anular', [App\Http\Controllers\CalendarioController::class, 'anularTarjeta'])->name('tarjetas.anular');
        // Factura Liquidation & Routes
        Route::get('facturas/{id}/liquidar', [App\Http\Controllers\FacturaController::class, 'liquidar'])->name('facturas.liquidar');
        Route::post('facturas/{id}/pagar-efectivo', [App\Http\Controllers\FacturaController::class, 'pagarEfectivo'])->name('facturas.pagar_efectivo');
        Route::post('facturas/pagar-cheque', [App\Http\Controllers\FacturaController::class, 'pagarCheque'])->name('facturas.pagar_cheque');
        Route::post('facturas/pagar-tarjeta', [App\Http\Controllers\FacturaController::class, 'pagarTarjeta'])->name('facturas.pagar_tarjeta');
        Route::post('facturas/pagar-deposito', [App\Http\Controllers\FacturaController::class, 'pagarDeposito'])->name('facturas.pagar_deposito');

        // Payments Module
        Route::get('/pagos', [App\Http\Controllers\PagoController::class, 'index'])->name('pagos.index');

        // Pedidos Online (Order Management)
        Route::resource('pedidos', App\Http\Controllers\PedidoController::class)->only(['index', 'show']);
        Route::patch('pedidos/{id}/status', [App\Http\Controllers\PedidoController::class, 'updateStatus'])->name('pedidos.updateStatus');
        Route::get('pedidos/productos/buscar', [App\Http\Controllers\PedidoController::class, 'searchProducts'])->name('pedidos.productos.buscar');
        Route::post('pedidos/{pedido}/detalles', [App\Http\Controllers\PedidoController::class, 'addDetail'])->name('pedidos.detalles.store');
        Route::post('pedidos/{pedido}/enviar', [App\Http\Controllers\PedidoController::class, 'markAsShipped'])->name('pedidos.enviar');

        // Configuración de Tarifas
        Route::post('configuracion/tarifa', [App\Http\Controllers\ConfiguracionController::class, 'storeTarifa'])->name('configuracion.tarifa.store');

        Route::patch('pedidos/{pedido}/detalles/{detalle}', [App\Http\Controllers\PedidoController::class, 'updateDetail'])->name('pedidos.detalles.update');
        Route::delete('pedidos/{pedido}/detalles/{detalle}', [App\Http\Controllers\PedidoController::class, 'destroyDetail'])->name('pedidos.detalles.destroy');

    });
});

require __DIR__ . '/auth.php';


Route::get('/ventas/nueva', [VentaController::class, 'iniciarVenta'])->name('ventas.nueva');
Route::patch('/ventas/{venta}/cerrar', [VentaController::class, 'cerrarVenta'])->name('ventas.cerrar');
Route::patch('/ventas/{venta}/reabrir', [VentaController::class, 'reabrir'])->name('ventas.reabrir');
Route::delete('/ventas/codigo/{id}', [VentaController::class, 'eliminarCodigoNoEncontrado'])->name('ventas.eliminar-codigo');
Route::get('/ventas/{venta}/eliminados', [VentaController::class, 'verEliminados'])->name('ventas.eliminados');

// Sitemap for SEO
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Route::get('/shop', [App\Http\Controllers\ShopController::class, 'shop'])->name('shop');
