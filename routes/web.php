<?php

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
Route::get('/contacto', [App\Http\Controllers\ShopController::class, 'contact'])->name('contact');



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
    });
});

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::resource('empresas', App\Http\Controllers\EmpresaController::class);
        Route::resource('facturas', App\Http\Controllers\FacturaController::class);

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


    });
});

require __DIR__ . '/auth.php';


Route::get('/ventas/nueva', [VentaController::class, 'iniciarVenta'])->name('ventas.nueva');
Route::patch('/ventas/{venta}/cerrar', [VentaController::class, 'cerrarVenta'])->name('ventas.cerrar');

// Sitemap for SEO
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Route::get('/shop', [App\Http\Controllers\ShopController::class, 'shop'])->name('shop');
