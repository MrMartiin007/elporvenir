<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Services\ImageVariants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortadaEjemplo extends Command
{
    protected $signature = 'portada:ejemplo {--force : Crearlos aunque ya existan banners}';

    protected $description = 'Crea 3 banners de ejemplo para la portada (editables desde el admin, para reemplazar por tus fotos)';

    /** Textos de ejemplo. Las imágenes viven en resources/portada-ejemplo y no llevan texto. */
    private const BANNERS = [
        1 => ['titulo' => 'Cuidado del cabello', 'subtitulo' => 'Alisados, keratinas y tratamientos de las mejores marcas', 'texto_boton' => 'Ver productos', 'enlace' => '/tienda?search=alisado'],
        2 => ['titulo' => 'Promociones del momento', 'subtitulo' => 'Descubre lo que tenemos para ti esta semana', 'texto_boton' => 'Ver la tienda', 'enlace' => '/tienda'],
        3 => ['titulo' => 'Maquillaje y skincare', 'subtitulo' => 'Todo para tu rutina de belleza', 'texto_boton' => 'Ver marcas', 'enlace' => '/marcas'],
    ];

    public function handle(ImageVariants $variants): int
    {
        if (Banner::deTipo(Banner::TIPO_BANNER)->exists() && !$this->option('force')) {
            $this->warn('Ya hay banners cargados; no se creó nada. Usa --force para agregar estos de todos modos.');

            return self::SUCCESS;
        }

        if (!ImageVariants::supported()) {
            $this->warn('PHP no tiene soporte WebP: los banners se crean igual, pero sin versiones livianas.');
        }

        $dir = resource_path('portada-ejemplo');

        foreach (self::BANNERS as $n => $datos) {
            $desktop = "$dir/banner-$n-desktop.jpg";
            $movil = "$dir/banner-$n-movil.jpg";
            if (!is_file($desktop) || !is_file($movil)) {
                $this->error("Falta la imagen de ejemplo $n en resources/portada-ejemplo.");

                return self::FAILURE;
            }

            $rutaDesktop = 'banners/' . Str::random(40) . '.jpg';
            $rutaMovil = 'banners/' . Str::random(40) . '.jpg';
            Storage::disk('public')->put($rutaDesktop, file_get_contents($desktop));
            Storage::disk('public')->put($rutaMovil, file_get_contents($movil));
            $variants->generate($rutaDesktop, false, ImageVariants::BANNER_WIDTHS);
            $variants->generate($rutaMovil, false, ImageVariants::BANNER_MOBILE_WIDTHS);

            Banner::create($datos + [
                'tipo' => Banner::TIPO_BANNER,
                'imagen' => $rutaDesktop,
                'imagen_movil' => $rutaMovil,
                'orden' => $n,
                'activo' => true,
            ]);
            $this->info("Banner $n creado: {$datos['titulo']}");
        }

        $this->line('Listo. Reemplaza las imágenes por tus fotos desde Administración > Portada.');

        return self::SUCCESS;
    }
}
