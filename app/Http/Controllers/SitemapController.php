<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        // El sitemap se regenera cada hora; no hace falta consultarlo en cada visita
        $xml = Cache::remember('sitemap.xml', now()->addHour(), fn () => $this->build());

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    private function build(): string
    {
        // Productos con precio (los únicos que se pueden comprar)
        $productos = Producto::with('marca')
            ->whereHas('ultimaEntrada')
            ->get();

        $ultimaActualizacion = $productos->max('updated_at');

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '
            . 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

        // Página principal: cambia cuando cambia el catálogo
        $sitemap .= $this->addUrl('https://elporvenir.com.gt/', $ultimaActualizacion?->toIso8601String());

        // Catálogo completo e índice de marcas
        $sitemap .= $this->addUrl(route('tienda'), $ultimaActualizacion?->toIso8601String());
        $sitemap .= $this->addUrl(route('tienda.marcas'), $ultimaActualizacion?->toIso8601String());

        // Contacto: sin fecha (no cambia con el catálogo; una fecha falsa le resta confianza al sitemap)
        $sitemap .= $this->addUrl('https://elporvenir.com.gt/contacto');

        // Una página por cada marca con productos
        $marcas = Marca::has('productos')->withMax('productos', 'updated_at')->get();
        foreach ($marcas as $marca) {
            $fecha = $marca->productos_max_updated_at
                ? \Illuminate\Support\Carbon::parse($marca->productos_max_updated_at)->toIso8601String()
                : null;
            $sitemap .= $this->addUrl($marca->url(), $fecha);
        }

        // Cada producto con su foto
        foreach ($productos as $producto) {
            $sitemap .= $this->addUrl($producto->url, $producto->updated_at?->toIso8601String(), $producto->imagen_url);
        }

        return $sitemap . '</urlset>';
    }

    /**
     * Helper para agregar una URL al sitemap
     */
    private function addUrl(string $loc, ?string $lastmod = null, ?string $imagen = null): string
    {
        $xml = '  <url>' . PHP_EOL . '    <loc>' . htmlspecialchars($loc, ENT_XML1) . '</loc>' . PHP_EOL;

        if ($lastmod) {
            $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
        }
        if ($imagen) {
            $xml .= '    <image:image><image:loc>' . htmlspecialchars($imagen, ENT_XML1) . '</image:loc></image:image>' . PHP_EOL;
        }

        return $xml . '  </url>' . PHP_EOL;
    }
}
