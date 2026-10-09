<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Genera versiones WebP livianas de las fotos de producto.
 *
 * El original no se toca: se crean copias en `productos/_w/{ancho}/{nombre}.webp`
 * y la tienda las sirve con <picture>/srcset. Si una versión no existe,
 * la vista usa el original, así que nada se rompe mientras se generan.
 */
class ImageVariants
{
    public const WIDTHS = [400, 800];
    /** Logos de marca: se muestran a ~50 px, no necesitan más. */
    public const LOGO_WIDTHS = [100, 200];
    /** Banners de la portada: celular, laptop y pantalla grande. */
    public const BANNER_WIDTHS = [640, 1280, 1920];
    /** Imagen de banner pensada para celular (vertical). */
    public const BANNER_MOBILE_WIDTHS = [480, 800];
    private const QUALITY = 80;
    private const DISK = 'public';

    public static function supported(): bool
    {
        return function_exists('imagewebp') && function_exists('imagecreatefromstring');
    }

    /** ¿Existe el archivo en el disco? (para mostrar un respaldo en vez de una imagen rota) */
    public static function existe(?string $path): bool
    {
        return filled($path) && Storage::disk(self::DISK)->exists($path);
    }

    /** Ruta relativa (en el disco public) de la variante de un ancho dado. */
    public static function variantPath(string $path, int $width): string
    {
        $dir = trim(dirname($path), './\\');
        $name = pathinfo($path, PATHINFO_FILENAME);

        return ($dir !== '' ? $dir . '/' : '') . '_w/' . $width . '/' . $name . '.webp';
    }

    /**
     * Crea las variantes que falten. Devuelve cuántas se generaron.
     * Nunca lanza excepciones: una imagen dañada no debe romper una subida.
     */
    public function generate(string $path, bool $force = false, array $widths = self::WIDTHS): int
    {
        if (!self::supported()) {
            return 0;
        }

        $disk = Storage::disk(self::DISK);
        if (!$disk->exists($path)) {
            return 0;
        }

        $pendientes = array_filter(
            $widths,
            fn ($w) => $force || !$disk->exists(self::variantPath($path, $w))
        );
        if (!$pendientes) {
            return 0;
        }

        try {
            $source = @imagecreatefromstring($disk->get($path));
            if (!$source) {
                return 0;
            }

            $source = $this->corregirOrientacion($source, $disk->path($path));
            $sw = imagesx($source);
            $sh = imagesy($source);
            $creadas = 0;

            foreach ($pendientes as $width) {
                // Nunca ampliar: si el original es menor, la variante conserva su tamaño
                $w = min($width, $sw);
                $h = max(1, (int) round($sh * $w / $sw));

                $canvas = imagecreatetruecolor($w, $h);
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 255, 255, 255, 127));
                imagecopyresampled($canvas, $source, 0, 0, 0, 0, $w, $h, $sw, $sh);

                ob_start();
                imagewebp($canvas, null, self::QUALITY);
                $binary = ob_get_clean();
                imagedestroy($canvas);

                if ($binary) {
                    $disk->put(self::variantPath($path, $width), $binary);
                    $creadas++;
                }
            }

            imagedestroy($source);

            return $creadas;
        } catch (\Throwable $e) {
            Log::warning('No se pudieron generar variantes de imagen', ['path' => $path, 'error' => $e->getMessage()]);

            return 0;
        }
    }

    /**
     * URL de una sola variante (para imágenes pequeñas que no necesitan srcset).
     * Si la variante no existe todavía, devuelve el original.
     */
    public static function url(string $path, int $width): string
    {
        $variante = self::variantPath($path, $width);

        return asset('storage/' . (Storage::disk(self::DISK)->exists($variante) ? $variante : $path));
    }

    /** Borra las variantes de una imagen (al reemplazarla o eliminarla). */
    public function delete(?string $path, array $widths = self::WIDTHS): void
    {
        if (!$path) {
            return;
        }

        foreach ($widths as $width) {
            Storage::disk(self::DISK)->delete(self::variantPath($path, $width));
        }
    }

    /**
     * Devuelve el srcset con las variantes que existen en disco, o null si no hay ninguna.
     * Ej: "/storage/productos/_w/400/a.webp 400w, /storage/productos/_w/800/a.webp 800w"
     */
    public static function srcset(?string $path, array $widths = self::WIDTHS): ?string
    {
        if (!$path) {
            return null;
        }

        $disk = Storage::disk(self::DISK);
        $partes = [];
        foreach ($widths as $width) {
            $variante = self::variantPath($path, $width);
            if ($disk->exists($variante)) {
                $partes[] = asset('storage/' . $variante) . ' ' . $width . 'w';
            }
        }

        return $partes ? implode(', ', $partes) : null;
    }

    private function corregirOrientacion(\GdImage $img, string $file): \GdImage
    {
        if (!function_exists('exif_read_data') || !preg_match('/\.jpe?g$/i', $file)) {
            return $img;
        }

        $exif = @exif_read_data($file);
        $angulo = match ($exif['Orientation'] ?? 1) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        return $angulo ? (imagerotate($img, $angulo, 0) ?: $img) : $img;
    }
}
