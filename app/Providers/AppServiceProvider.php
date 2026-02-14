<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartir la variable $pedidosPendientes con todas las vistas
        // Usamos un view composer o directamente share, pero con cuidado de que la tabla exista
        // para evitar errores al correr migraciones frescas.

        try {
            if (\Schema::hasTable('pedidos')) {
                view()->share('pedidosPendientes', \App\Models\Pedido::where('estado', 'pendiente')->count());
            } else {
                view()->share('pedidosPendientes', 0);
            }
        } catch (\Exception $e) {
            // En caso de error (ej. durante instalación), definimos 0
            view()->share('pedidosPendientes', 0);
        }

        // Compartir tarifa de envío actual
        try {
            if (\Schema::hasTable('tarifas_envios')) {
                $tarifa = \App\Models\TarifaEnvio::latest()->first();
                view()->share('tarifaActual', $tarifa);
            } else {
                view()->share('tarifaActual', null);
            }
        } catch (\Exception $e) {
            view()->share('tarifaActual', null);
        }
    }
}
