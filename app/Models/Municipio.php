<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'nombre',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    /**
     * Obtener el departamento del municipio
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Obtener pedidos de este municipio
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Scope para municipios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
