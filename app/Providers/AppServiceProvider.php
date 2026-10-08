<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        // Fuera de local, todas las URLs generadas (canonical, sitemap, JSON-LD)
        // usan siempre el dominio oficial con https, sin depender del .env ni del proxy.
        if (!$this->app->environment('local')) {
            URL::forceRootUrl('https://elporvenir.com.gt');
            URL::forceScheme('https');
        }

        // $pedidosPendientes y $tarifaActual solo los usan el panel de administración
        // (layouts.app) y el carrito. Antes se calculaban en TODAS las peticiones,
        // incluida la tienda pública; con un composer solo se consultan donde hacen falta.
        // El try/catch evita errores si las tablas aún no existen (migraciones frescas).
        View::composer('layouts.app', function ($view) {
            $view->with('pedidosPendientes', $this->seguro(fn () => \App\Models\Pedido::where('estado', 'pendiente')->count(), 0));
            $view->with('tarifaActual', $this->seguro(fn () => \App\Models\TarifaEnvio::latest()->first()));
        });

        View::composer('carrito.index', function ($view) {
            $view->with('tarifaActual', $this->seguro(fn () => \App\Models\TarifaEnvio::latest()->first()));
        });
    }

    private function seguro(\Closure $consulta, mixed $porDefecto = null): mixed
    {
        try {
            return $consulta();
        } catch (\Throwable $e) {
            return $porDefecto;
        }
    }
}
