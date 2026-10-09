<?php

namespace App\Services;

use App\Models\CodigoNoEncontrado;
use App\Models\Producto;
use App\Models\ProductoCodigo;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Reglas de los códigos de barras de un producto: un producto tiene un código principal
 * (productos.codigo_producto) y puede tener los adicionales que haga falta (producto_codigos).
 * Ningún código puede estar en dos productos, ni repetido dentro del mismo.
 */
class CodigosProducto
{
    /**
     * Formas equivalentes de un mismo código de barras. El mismo producto puede venir como UPC de 12 dígitos,
     * como EAN de 13 (con un 0 delante) o como GTIN-14 (con dos). Para códigos con letras, solo el código tal cual.
     */
    public static function variantes(string $codigo): array
    {
        $codigo = trim($codigo);
        if ($codigo === '') {
            return [];
        }

        $variantes = [$codigo];

        if (ctype_digit($codigo)) {
            $base = ltrim($codigo, '0');
            if ($base !== '') {
                $variantes[] = $base;
                foreach (['0', '00', '000'] as $ceros) {
                    $variantes[] = $ceros . $base;
                }
            }
        }

        return array_values(array_unique($variantes));
    }

    /** ¿Son el mismo código aunque difieran en ceros a la izquierda? */
    public static function mismoCodigo(string $a, string $b): bool
    {
        // La base de datos no distingue mayúsculas de minúsculas en los códigos, así que aquí tampoco
        $minus = fn (array $lista) => array_map('mb_strtolower', $lista);

        return count(array_intersect($minus(self::variantes($a)), $minus(self::variantes($b)))) > 0;
    }

    /**
     * Producto al que ya pertenece este código (o una forma equivalente), principal o adicional.
     * Con $ignorarProductoId se omite ese producto (para editarlo sin chocar consigo mismo).
     *
     * @return array{producto: Producto, tipo: string, codigo: string}|null
     */
    public static function duenoDe(string $codigo, ?int $ignorarProductoId = null): ?array
    {
        $variantes = self::variantes($codigo);
        if (!$variantes) {
            return null;
        }

        $principal = Producto::whereIn('codigo_producto', $variantes)
            ->when($ignorarProductoId, fn ($q) => $q->where('id', '!=', $ignorarProductoId))
            ->first();
        if ($principal) {
            return ['producto' => $principal, 'tipo' => 'principal', 'codigo' => $principal->codigo_producto];
        }

        $adicional = ProductoCodigo::with('producto')->whereIn('codigo', $variantes)
            ->when($ignorarProductoId, fn ($q) => $q->where('productos_id', '!=', $ignorarProductoId))
            ->first();
        if ($adicional && $adicional->producto) {
            return ['producto' => $adicional->producto, 'tipo' => 'adicional', 'codigo' => $adicional->codigo];
        }

        return null;
    }

    /** Mensaje para el usuario cuando un código ya está registrado. */
    public static function mensajeEnUso(string $codigo, array $dueno): string
    {
        $nombre = $dueno['producto']->detalle_producto;
        $detalle = $dueno['tipo'] === 'principal' ? 'código principal' : 'código adicional';
        $mismo = trim($codigo) === $dueno['codigo'] ? '' : " (registrado como {$dueno['codigo']})";

        return "El código {$codigo} ya está registrado: es el {$detalle} del producto «{$nombre}»{$mismo}.";
    }

    /** Limpia la lista de códigos extra del formulario: sin espacios, sin vacíos y sin repetidos (ni por mayúsculas). */
    public static function limpiar(array $codigos): array
    {
        $limpios = [];
        foreach ($codigos as $c) {
            $c = trim((string) $c);
            if ($c !== '' && !isset($limpios[mb_strtolower($c)])) {
                $limpios[mb_strtolower($c)] = $c;
            }
        }

        return array_values($limpios);
    }

    /**
     * Agrega al validador los errores de códigos repetidos DENTRO del formulario
     * (el principal contra los extras, y los extras entre sí).
     */
    public static function validarLista(Validator $validator, ?string $principal, array $extras): void
    {
        $vistos = [];
        if ($principal !== null && trim($principal) !== '') {
            $vistos[] = trim($principal);
        }

        foreach ($extras as $i => $extra) {
            $extra = trim((string) $extra);
            if ($extra === '') {
                continue;
            }

            foreach ($vistos as $anterior) {
                if (self::mismoCodigo($extra, $anterior)) {
                    $validator->errors()->add("codigos_extra.$i", "El código {$extra} está repetido en este mismo formulario.");
                    break;
                }
            }
            $vistos[] = $extra;
        }
    }

    /**
     * Deja los códigos adicionales del producto exactamente como vienen en el formulario:
     * quita los que ya no están y agrega los nuevos. También limpia esos códigos de la lista
     * de "códigos no encontrados" en ventas, porque ya tienen dueño.
     */
    public static function sincronizar(Producto $producto, array $extras): void
    {
        $extras = self::limpiar($extras);

        DB::transaction(function () use ($producto, $extras) {
            $producto->codigos()->whereNotIn('codigo', $extras)->delete();

            $existentes = $producto->codigos()->pluck('codigo')->all();
            foreach ($extras as $codigo) {
                if (!in_array($codigo, $existentes, true)) {
                    $producto->codigos()->create(['codigo' => $codigo]);
                }
            }

            $todos = array_merge([$producto->codigo_producto], $extras);
            $variantes = collect($todos)->flatMap(fn ($c) => self::variantes((string) $c))->unique()->all();
            CodigoNoEncontrado::whereIn('codigo', $variantes)->delete();
        });
    }
}
