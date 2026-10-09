<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    public const TIPO_BANNER = 'banner';
    public const TIPO_VIDEO = 'video';

    protected $fillable = [
        'tipo', 'titulo', 'subtitulo', 'texto_boton', 'enlace',
        'imagen', 'imagen_movil', 'video',
        'orden', 'activo', 'inicia_en', 'termina_en',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'inicia_en' => 'datetime',
        'termina_en' => 'datetime',
    ];

    /** Activos y dentro de su rango de fechas (si lo tienen). */
    public function scopeVigentes(Builder $q): Builder
    {
        return $q->where('activo', true)
            ->where(fn ($q) => $q->whereNull('inicia_en')->orWhere('inicia_en', '<=', now()))
            ->where(fn ($q) => $q->whereNull('termina_en')->orWhere('termina_en', '>=', now()));
    }

    public function scopeDeTipo(Builder $q, string $tipo): Builder
    {
        return $q->where('tipo', $tipo);
    }

    public function getEsVideoAttribute(): bool
    {
        return $this->tipo === self::TIPO_VIDEO;
    }

    /** Estado legible para el panel: Activo, Programado, Vencido o Apagado. */
    public function getEstadoAttribute(): string
    {
        if (!$this->activo) {
            return 'Apagado';
        }
        if ($this->inicia_en && $this->inicia_en->isFuture()) {
            return 'Programado';
        }
        if ($this->termina_en && $this->termina_en->isPast()) {
            return 'Vencido';
        }

        return 'Activo';
    }

    /** URL de destino: acepta rutas internas ("/tienda") o enlaces completos. */
    public function getEnlaceUrlAttribute(): ?string
    {
        if (!$this->enlace) {
            return null;
        }

        return str_starts_with($this->enlace, '/') ? url($this->enlace) : $this->enlace;
    }
}
