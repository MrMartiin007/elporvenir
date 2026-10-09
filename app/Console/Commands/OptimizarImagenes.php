<?php

namespace App\Console\Commands;

use App\Models\Marca;
use App\Models\Producto;
use App\Services\ImageVariants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizarImagenes extends Command
{
    protected $signature = 'imagenes:optimizar {--force : Regenerar aunque ya existan}';

    protected $description = 'Genera versiones WebP de las fotos de productos (400/800 px) y logos de marcas (100/200 px)';

    public function handle(ImageVariants $variants): int
    {
        if (!ImageVariants::supported()) {
            $this->error('PHP no tiene soporte WebP en GD (imagewebp). Activa la extensión gd con WebP.');

            return self::FAILURE;
        }

        $force = (bool) $this->option('force');

        $this->procesar('productos', Producto::class, 'foto_producto', ImageVariants::WIDTHS, $variants, $force);
        $this->procesar('marcas', Marca::class, 'foto_marca', ImageVariants::LOGO_WIDTHS, $variants, $force);

        return self::SUCCESS;
    }

    private function procesar(string $etiqueta, string $modelo, string $columna, array $widths, ImageVariants $variants, bool $force): void
    {
        $query = $modelo::whereNotNull($columna)->where($columna, '!=', '');
        $total = (clone $query)->count();
        $this->info("Procesando {$total} {$etiqueta} con foto...");

        $bar = $this->output->createProgressBar($total);
        $generadas = 0;
        $sinArchivo = 0;

        $query->select('id', $columna)->chunkById(50, function ($filas) use ($columna, $widths, $variants, $force, $bar, &$generadas, &$sinArchivo) {
            foreach ($filas as $fila) {
                $ruta = $fila->{$columna};
                $generadas += $variants->generate($ruta, $force, $widths);
                if (!Storage::disk('public')->exists($ruta)) {
                    $sinArchivo++;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("Variantes generadas ({$etiqueta}): {$generadas}");
        if ($sinArchivo) {
            $this->warn("{$etiqueta} cuyo archivo no existe en disco: {$sinArchivo}");
        }
    }
}
