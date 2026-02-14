<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    /**
     * Obtener los municipios del departamento
     */
    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }

    /**
     * Obtener solo municipios activos
     */
    public function municipiosActivos()
    {
        return $this->hasMany(Municipio::class)->where('activo', true);
    }

    /**
     * Obtener pedidos de este departamento
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Scope para departamentos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
