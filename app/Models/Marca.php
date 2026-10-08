<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class Marca
 *
 * @property $id
 * @property $nombre_marca
 * @property $foto_marca
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Marca extends Model
{
    

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['nombre_marca', 'foto_marca'];


public function productos()
{
    return $this->hasMany(Producto::class, 'marcas_id');
}

    /**
     * Las marcas más vendidas en tienda en los últimos 90 días (para los atajos del encabezado).
     * Si hay pocas ventas, completa con las marcas que tienen más productos. Se guarda 1 hora.
     */
    public static function populares(int $cuantas = 4)
    {
        return Cache::remember("marcas.populares.{$cuantas}", 3600, function () use ($cuantas) {
            $ids = DB::table('detalle_ventas as d')
                ->join('productos as p', 'p.id', '=', 'd.productos_id')
                ->where('d.created_at', '>=', now()->subDays(90))
                ->whereNotNull('p.marcas_id')
                ->groupBy('p.marcas_id')
                ->orderByRaw('SUM(CAST(d.cantidad AS UNSIGNED)) DESC')
                ->limit($cuantas)
                ->pluck('p.marcas_id');

            if ($ids->count() < $cuantas) {
                $faltan = self::has('productos')->whereNotIn('id', $ids)
                    ->withCount('productos')->orderByDesc('productos_count')
                    ->limit($cuantas - $ids->count())->pluck('id');
                $ids = $ids->concat($faltan);
            }

            return self::whereIn('id', $ids)->get()
                ->sortBy(fn ($m) => $ids->search($m->id))->values();
        });
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->nombre_marca) ?: 'marca';
    }

    /** URL pública de la marca: /marca/{id}-{slug} (más parámetros opcionales como sort o page). */
    public function url(array $query = []): string
    {
        return route('marca.show', ['id' => $this->id, 'slug' => $this->slug] + array_filter($query));
    }

}
