<?php

namespace App\Http\Controllers;


use App\Models\Banner;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Marca;
use App\Helpers\IdObfuscator;
use Carbon\Carbon;

use App\Models\Factura;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{

    /**
     * Portada: banners, marcas, ofertas, novedades y más vendidos.
     */
    public function home(Request $request)
    {
        // El catálogo vivía en la raíz. Las URLs antiguas (/?search=, /?marca=, /?page=2, /?sort=)
        // se redirigen con 301 para conservar el posicionamiento y los enlaces ya compartidos.
        if ($request->hasAny(['search', 'marca', 'page', 'sort'])) {
            if ($request->filled('marca') && !$request->filled('search')) {
                $marca = Marca::find($request->input('marca'));
                if ($marca) {
                    return redirect()->to($marca->url($request->only(['sort', 'page'])), 301);
                }
            }

            return redirect()->to(route('tienda', $request->query()), 301);
        }

        // Los banners no se guardan en caché: al apagar o programar uno el cambio es inmediato.
        $banners = Banner::vigentes()->deTipo(Banner::TIPO_BANNER)->orderBy('orden')->orderBy('id')->get();
        $video = Banner::vigentes()->deTipo(Banner::TIPO_VIDEO)->orderBy('orden')->orderBy('id')->first();

        // Las secciones de productos se guardan en caché para que la portada sea rápida. Se limpian solas
        // al crear/editar/borrar un producto o una entrada (modelos Producto y Entrada); los cambios de
        // stock por ventas se reflejan como máximo en 60 segundos.
        $secciones = Cache::remember('portada.secciones', 60, fn () => $this->seccionesPortada());

        $carritoCount = collect(session()->get('carrito', []))->sum('cantidad');

        return view('portada', $secciones + compact('banners', 'video', 'carritoCount'));
    }

    /**
     * Catálogo completo: /tienda
     */
    public function tienda(Request $request)
    {
        // URL antigua ?marca=ID  →  URL propia de la marca (301, conserva el posicionamiento).
        // Si además hay búsqueda se mantiene el comportamiento anterior.
        if ($request->filled('marca') && !$request->filled('search')) {
            $marca = Marca::find($request->input('marca'));
            if ($marca) {
                return redirect()->to($marca->url($request->only(['sort', 'page'])), 301);
            }
        }

        return $this->catalogo($request);
    }

    /**
     * Sugerencias del buscador mientras se escribe: /buscar?q=ali
     * Devuelve hasta 6 productos y 3 marcas. Con menos de 2 letras no busca.
     */
    public function sugerencias(Request $request)
    {
        $q = trim(mb_substr((string) $request->query('q', ''), 0, 60));
        if (mb_strlen($q) < 2) {
            return response()->json(['productos' => [], 'marcas' => [], 'todos' => null]);
        }

        // Los comodines de LIKE escritos por el usuario se tratan como texto normal
        $like = '%' . addcslashes($q, '%_\\') . '%';

        $productos = Producto::with(['marca', 'ultimaEntrada'])
            ->whereHas('ultimaEntrada')
            ->buscar($q)
            ->porRelevancia($q)
            ->limit(12)->get()
            ->filter(fn ($p) => $p->precio_actual !== null)
            ->take(6)
            ->map(fn ($p) => [
                'nombre' => $p->detalle_producto,
                'marca' => $p->marca->nombre_marca ?? '',
                'precio' => number_format((float) $p->precio_actual, 2),
                'agotado' => !$p->en_stock,
                'url' => $p->url,
                'imagen' => \App\Services\ImageVariants::existe($p->foto_producto)
                    ? \App\Services\ImageVariants::url($p->foto_producto, 400)
                    : null,
            ])->values();

        $marcas = Marca::has('productos')->where('nombre_marca', 'like', $like)
            ->orderBy('nombre_marca')->limit(3)->get()
            ->map(fn ($m) => ['nombre' => $m->nombre_marca, 'url' => $m->url()])->values();

        return response()->json([
            'productos' => $productos,
            'marcas' => $marcas,
            'todos' => route('tienda', ['search' => $q]),
        ]);
    }

    /** Productos comprables para la portada: con foto, precio y stock. */
    private function productosPortada()
    {
        return Producto::with(['marca', 'ultimaEntrada'])
            ->whereHas('ultimaEntrada')
            ->whereNotNull('foto_producto')->where('foto_producto', '!=', '')
            ->whereRaw('CAST(stock AS UNSIGNED) > 0');
    }

    private function seccionesPortada(): array
    {
        $comprable = fn ($p) => $p->precio_actual !== null;

        $marcasLogo = Marca::has('productos')->withCount('productos')
            ->whereNotNull('foto_marca')->where('foto_marca', '!=', '')
            ->orderByDesc('productos_count')->limit(24)->get();

        $ofertas = $this->productosPortada()
            ->whereRaw('CAST(oferta AS UNSIGNED) > 0')
            ->orderByDesc('updated_at')->limit(12)->get()->filter($comprable)->take(8)->values();

        $novedades = $this->productosPortada()
            ->orderByDesc('created_at')->orderByDesc('id')->limit(16)->get()->filter($comprable)->take(8)->values();

        // Más vendidos: unidades vendidas en tienda en los últimos 90 días (si hay pocas, todo el historial)
        $topIds = fn ($desde) => DetalleVenta::query()
            ->when($desde, fn ($q) => $q->where('created_at', '>=', $desde))
            ->whereNotNull('productos_id')
            ->groupBy('productos_id')
            ->orderByRaw('SUM(CAST(cantidad AS UNSIGNED)) DESC')
            ->limit(60)->pluck('productos_id');

        $ids = $topIds(now()->subDays(90));
        if ($ids->count() < 8) {
            $ids = $topIds(null);
        }

        $masVendidos = $ids->isEmpty() ? collect() : $this->productosPortada()
            ->whereIn('id', $ids)->get()->filter($comprable)
            ->sortBy(fn ($p) => $ids->search($p->id))->take(8)->values();

        $marcasTotal = Marca::has('productos')->count();

        return compact('marcasLogo', 'marcasTotal', 'ofertas', 'novedades', 'masVendidos');
    }

    /**
     * Índice de todas las marcas: /marcas
     */
    public function marcas()
    {
        $marcas = Marca::has('productos')->withCount('productos')->orderBy('nombre_marca')->get();

        return view('marcas', compact('marcas'));
    }

    /**
     * Página de una marca: /marca/{id}-{slug}
     */
    public function marca(Request $request, $id, $slug = null)
    {
        $marca = Marca::has('productos')->findOrFail($id);

        // Slug incorrecto o ausente → URL canónica
        if ($slug !== $marca->slug) {
            return redirect()->to($marca->url($request->only(['sort', 'page'])), 301);
        }

        return $this->catalogo($request, $marca);
    }

    private function catalogo(Request $request, ?Marca $marcaActual = null)
    {
        $search = $request->input('search');
        $marcaId = $marcaActual?->id ?? $request->input('marca');

        // Búsqueda por palabras: "alisado kativa" encuentra Kativa aunque la marca esté en otro campo
        $query = Producto::with(['marca', 'ultimaEntrada'])
            ->when($search, fn ($q, $texto) => $q->buscar($texto));

        if ($marcaId) {
            $query->where('marcas_id', $marcaId);
        }

        // Sorting
        $sort = $request->input('sort');

        // Actually, let's stick to the previous 'ultimaEntrada' logic. sorting by a hasOne latest relation requires join.
        // Simplified approach for now:
        switch ($sort) {
            case 'price_asc':
                $query->join('entradas', function ($join) {
                    $join->on('entradas.productos_id', '=', 'productos.id')
                        ->whereRaw('entradas.id = (select id from entradas where entradas.productos_id = productos.id order by created_at desc limit 1)');
                })
                    ->orderByDesc('productos.oferta')
                    ->orderBy('entradas.precio_venta', 'asc')
                    ->select('productos.*');
                break;
            case 'price_desc':
                $query->join('entradas', function ($join) {
                    $join->on('entradas.productos_id', '=', 'productos.id')
                        ->whereRaw('entradas.id = (select id from entradas where entradas.productos_id = productos.id order by created_at desc limit 1)');
                })
                    ->orderByDesc('productos.oferta')
                    ->orderBy('entradas.precio_venta', 'desc')
                    ->select('productos.*');
                break;
            case 'oldest':
                $query->orderByDesc('oferta')->orderBy('updated_at', 'asc');
                break;
            default: // newest
                $query->orderByDesc('oferta')->orderBy('updated_at', 'desc');
                break;
        }

        $productos = $query->paginate(12);  // 12 products: 6 rows of 2 (mobile) or 4 rows of 3 (desktop)

        // Fix SEO Soft 404s: If the requested page is beyond the last page
        if ($productos->isEmpty() && $productos->currentPage() > 1) {
            abort(404);
        }
        $marcas = Marca::has('productos')->withCount('productos')->orderBy('nombre_marca')->get();

        // Contador del carrito para el navbar
        $carritoCount = collect(session()->get('carrito', []))->sum('cantidad');

        return view('shop', compact('productos', 'marcas', 'search', 'marcaId', 'marcaActual', 'sort', 'carritoCount'));
    }

    public function showProduct($hash, $slug = null)
    {
        $id = IdObfuscator::decode($hash);

        $producto = null;
        if ($id) {
            $producto = Producto::with(['marca', 'ultimaEntrada', 'entradas'])->find($id);
        }

        // Fallback for legacy URLs (e.g. /producto/2814) that somehow made it here
        if (!$producto && is_numeric($hash)) {
            $legacyProduct = Producto::find((int) $hash);
            if ($legacyProduct) {
                $newHash = IdObfuscator::encode($legacyProduct->id);
                $newSlug = \Illuminate\Support\Str::slug($legacyProduct->detalle_producto);
                return redirect()->route('producto.show', ['hash' => $newHash, 'slug' => $newSlug], 301);
            }
        }

        if (!$producto) {
            abort(404);
        }

        // Productos relacionados (misma marca, excluyendo el actual)
        $relacionados = collect();
        if ($producto->marcas_id) {
            $relacionados = Producto::with(['marca', 'ultimaEntrada'])
                ->where('marcas_id', $producto->marcas_id)
                ->where('id', '!=', $producto->id)
                ->whereHas('ultimaEntrada')
                ->inRandomOrder()
                ->take(4)
                ->get();
        }

        $carritoCount = collect(session()->get('carrito', []))->sum('cantidad');
        $enCarrito = session('carrito') && isset(session('carrito')[$producto->id]);

        return view('product-detail', compact('producto', 'relacionados', 'carritoCount', 'enCarrito'));
    }

    public function contact()
    {
        return view('contact');
    }
}