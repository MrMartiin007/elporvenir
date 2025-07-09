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

Route::get('/', function () {
    return view('auth.login');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::middleware(['auth', 'role:superadmin|venta'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::resource('productos', ProductoController::class);
        Route::resource('marcas', MarcaController::class);
        Route::resource('entradas', EntradaController::class);
        Route::resource('ventas', VentaController::class);
        Route::resource('detalle-ventas', DetalleVenta::class);
        Route::get('/producto/buscar', [VentaController::class, 'buscarProducto'])->name('productos.buscar');
        Route::get('/producto/consultar', [ProductoController::class, 'consultarProducto'])->name('productos.consultar');


    });
});

require __DIR__ . '/auth.php';


Route::get('/ventas/nueva', [VentaController::class, 'iniciarVenta'])->name('ventas.nueva');
Route::patch('/ventas/{venta}/cerrar', [VentaController::class, 'cerrarVenta'])->name('ventas.cerrar');

