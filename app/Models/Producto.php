<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Helpers\IdObfuscator;

/**
 * Class Producto
 *
 * @property $id
 * @property $codigo_producto
 * @property $detalle_producto
 * @property $foto_producto
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Producto extends Model
{


    protected $perPage = 20;

    /** Al crear, editar o borrar un producto la portada se recalcula en la siguiente visita. */
    protected static function booted()
    {
        $olvidar = fn () => \Illuminate\Support\Facades\Cache::forget('portada.secciones');
        static::saved($olvidar);
        static::deleted($olvidar);
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['codigo_producto', 'detalle_producto', 'foto_producto', 'marcas_id', 'oferta', 'stock'];


    /**
     * Búsqueda inteligente: cada palabra debe aparecer en el nombre, el código o la marca.
     * "alisado kativa" encuentra productos Kativa con "alisado" en el nombre, aunque la marca esté en otro campo.
     * Los comodines de LIKE escritos por el usuario se tratan como texto normal.
     */
    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        $terminos = collect(preg_split('/\s+/u', trim((string) $texto)))->filter()->take(6);

        foreach ($terminos as $termino) {
            $like = '%' . addcslashes($termino, '%_\\') . '%';
            // Versión sin guiones, apóstrofes ni puntos: "anticaida" encuentra "ANTI-CAIDA" y "ponds" encuentra "POND´S"
            $limpio = preg_replace("/[-'´`.]/u", '', $termino);
            $likeLimpio = '%' . addcslashes($limpio, '%_\\') . '%';

            $query->where(function ($w) use ($like, $limpio, $likeLimpio) {
                $w->where('detalle_producto', 'like', $like)
                    ->orWhere('codigo_producto', 'like', $like)
                    ->orWhereHas('codigos', fn ($c) => $c->where('codigo', 'like', $like))
                    ->orWhereHas('marca', function ($m) use ($like, $limpio, $likeLimpio) {
                        $m->where('nombre_marca', 'like', $like);
                        if ($limpio !== '') {
                            $m->orWhereRaw(self::sinPuntuacion('nombre_marca') . ' like ?', [$likeLimpio]);
                        }
                    });
                if ($limpio !== '') {
                    $w->orWhereRaw(self::sinPuntuacion('detalle_producto') . ' like ?', [$likeLimpio]);
                }
            });
        }

        return $query;
    }

    /** Expresión SQL que quita guiones, apóstrofes y puntos de una columna (solo para comparar). */
    private static function sinPuntuacion(string $columna): string
    {
        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE({$columna}, '-', ''), '´', ''), '\\'', ''), '`', ''), '.', '')";
    }

    /**
     * Orden por relevancia para los resultados de búsqueda: código exacto, nombre que empieza igual,
     * con existencias primero y los más recientes.
     */
    public function scopePorRelevancia(Builder $query, string $texto): Builder
    {
        $texto = trim($texto);

        return $query
            ->orderByRaw('(codigo_producto = ? OR EXISTS (SELECT 1 FROM producto_codigos pc WHERE pc.productos_id = productos.id AND pc.codigo = ?)) DESC', [$texto, $texto])
            ->orderByRaw('(detalle_producto LIKE ?) DESC', [addcslashes($texto, '%_\\') . '%'])
            ->orderByRaw('(CAST(stock AS UNSIGNED) > 0) DESC')
            ->orderByDesc('updated_at');
    }

    /** Códigos de barras ADICIONALES. El principal está en la columna codigo_producto. */
    public function codigos()
    {
        return $this->hasMany(ProductoCodigo::class, 'productos_id');
    }

    /**
     * Productos que tienen este código, ya sea como principal o como adicional.
     * Acepta también el mismo código con ceros de más o de menos a la izquierda (UPC de 12 / EAN de 13).
     * Si hay coincidencia exacta, va primero.
     */
    public function scopePorCodigo(Builder $query, string $codigo): Builder
    {
        $variantes = \App\Services\CodigosProducto::variantes($codigo);

        return $query
            ->where(function ($w) use ($variantes) {
                $w->whereIn('codigo_producto', $variantes)
                    ->orWhereHas('codigos', fn ($c) => $c->whereIn('codigo', $variantes));
            })
            ->orderByRaw('(codigo_producto = ?) DESC', [trim($codigo)]);
    }

    /** Todos los códigos del producto: primero el principal y luego los adicionales. */
    public function todosLosCodigos(): array
    {
        return array_values(array_filter(array_merge(
            [$this->codigo_producto],
            $this->codigos->pluck('codigo')->all()
        )));
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marcas_id');
    }
    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'productos_id');
    }

    public function ultimaEntrada()
    {
        return $this->hasOne(Entrada::class, 'productos_id')->latestOfMany();
    }

    public function getSlugAttribute()
    {
        $marca = $this->marca ? $this->marca->nombre_marca : '';
        return Str::slug($marca . '-' . $this->detalle_producto);
    }

    public function getHashIdAttribute()
    {
        return IdObfuscator::encode($this->id);
    }

    /** URL pública canónica del producto. */
    public function getUrlAttribute(): string
    {
        return route('producto.show', ['hash' => $this->hash_id, 'slug' => $this->slug]);
    }

    /** URL de la foto original, o null si no tiene. */
    public function getImagenUrlAttribute(): ?string
    {
        return $this->foto_producto ? asset('storage/' . $this->foto_producto) : null;
    }

    /** Precio vigente con punto decimal (ej. "85.00"), o null si no tiene precio. */
    public function getPrecioActualAttribute(): ?string
    {
        $precio = $this->ultimaEntrada?->precio_venta;

        return is_numeric($precio) && (float) $precio > 0 ? number_format((float) $precio, 2, '.', '') : null;
    }

    public function getEnStockAttribute(): bool
    {
        return (int) $this->stock > 0;
    }

    /**
     * El código del producto como GTIN (código de barras), solo si es válido:
     * 8, 12, 13 o 14 dígitos y dígito de control correcto. Si no, null.
     */
    public function getGtinAttribute(): ?string
    {
        foreach ($this->todosLosCodigos() as $codigo) {
            if (self::esGtinValido($codigo)) {
                return trim($codigo);
            }
        }

        return null;
    }

    private static function esGtinValido(string $codigo): bool
    {
        $codigo = trim($codigo);
        if (!ctype_digit($codigo) || !in_array(strlen($codigo), [8, 12, 13, 14], true)) {
            return false;
        }

        $digitos = array_map('intval', str_split($codigo));
        $control = array_pop($digitos);
        $suma = 0;
        foreach (array_reverse($digitos) as $i => $n) {
            $suma += $n * ($i % 2 === 0 ? 3 : 1);
        }

        return ((10 - $suma % 10) % 10) === $control;
    }

}
