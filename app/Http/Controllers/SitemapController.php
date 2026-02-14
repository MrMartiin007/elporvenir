<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        // Obtener todos los productos con stock disponible
        $productos = Producto::with(['marca', 'ultimaEntrada'])
            ->whereHas('ultimaEntrada')
            ->get();

        // Crear el XML del sitemap
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Página principal
        $sitemap .= $this->addUrl('https://elporvenir.shop/', '1.0', 'daily', now()->toIso8601String());

        // Página de contacto
        $sitemap .= $this->addUrl('https://elporvenir.shop/contacto', '0.8', 'weekly', now()->toIso8601String());

        // Agregar cada producto con su URL individual
        foreach ($productos as $producto) {
            $sitemap .= $this->addUrl(
                route('producto.show', $producto->id),
                '0.6',
                'weekly',
                $producto->updated_at->toIso8601String()
            );
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Helper para agregar una URL al sitemap
     */
    private function addUrl($loc, $priority, $changefreq, $lastmod)
    {
        return '  <url>' . PHP_EOL .
            '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL .
            '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL .
            '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL .
            '    <priority>' . $priority . '</priority>' . PHP_EOL .
            '  </url>' . PHP_EOL;
    }
}
