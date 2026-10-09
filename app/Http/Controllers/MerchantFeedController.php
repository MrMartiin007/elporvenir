<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Support\Facades\Cache;
use XMLWriter;

/**
 * Feed de productos para Google Merchant Center (fichas gratuitas en Google Shopping).
 * URL a registrar en Merchant Center: https://elporvenir.com.gt/feeds/google-merchant.xml
 */
class MerchantFeedController extends Controller
{
    public function google()
    {
        $xml = Cache::remember('feed.google-merchant', now()->addHour(), fn () => $this->build());

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function build(): string
    {
        // Google exige foto, precio y enlace. Los productos sin ellos no se publican.
        $productos = Producto::with(['marca', 'ultimaEntrada'])
            ->whereHas('ultimaEntrada')
            ->whereNotNull('foto_producto')
            ->where('foto_producto', '!=', '')
            ->get()
            ->filter(fn (Producto $p) => $p->precio_actual !== null);

        $w = new XMLWriter();
        $w->openMemory();
        $w->setIndent(true);
        $w->startDocument('1.0', 'UTF-8');
        $w->startElement('rss');
        $w->writeAttribute('version', '2.0');
        $w->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
        $w->startElement('channel');
        $w->writeElement('title', 'El Porvenir Beauty Center');
        $w->writeElement('link', 'https://elporvenir.com.gt/');
        $w->writeElement('description', 'Cosméticos y cuidado personal en Puerto Barrios, Guatemala');

        foreach ($productos as $p) {
            $marca = $p->marca->nombre_marca ?? null;
            $titulo = $p->detalle_producto;
            if ($marca && stripos($titulo, $marca) === false) {
                $titulo = $marca . ' ' . $titulo;
            }

            $w->startElement('item');
            $w->writeElement('g:id', (string) $p->id);
            $w->writeElement('g:title', mb_substr($titulo, 0, 150));
            $w->writeElement('g:description', $p->detalle_producto
                . ($marca ? ' de ' . $marca : '')
                . '. Disponible en El Porvenir Beauty Center, Puerto Barrios, con envío a toda Guatemala.');
            $w->writeElement('g:link', $p->url);
            $w->writeElement('g:image_link', $p->imagen_url);
            $w->writeElement('g:availability', $p->en_stock ? 'in_stock' : 'out_of_stock');
            $w->writeElement('g:price', $p->precio_actual . ' GTQ');
            $w->writeElement('g:condition', 'new');
            if ($marca) {
                $w->writeElement('g:brand', $marca);
            }
            if ($p->gtin) {
                $w->writeElement('g:gtin', $p->gtin);
            } else {
                // Sin código de barras válido: se declara para que Google no lo rechace
                $w->writeElement('g:identifier_exists', 'no');
            }
            $w->endElement(); // item
        }

        $w->endElement(); // channel
        $w->endElement(); // rss
        $w->endDocument();

        return $w->outputMemory();
    }
}
