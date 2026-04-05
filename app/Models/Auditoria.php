<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Auditoria
 *
 * @property $id
 * @property $fecha_auditoria
 * @property $cantidad_productos
 * @property $total_auditado
 * @property $estado
 * @property $users_id
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Auditoria extends Model
{
    protected $table = 'auditorias';

    protected $fillable = [
        'fecha_auditoria',
        'cantidad_productos',
        'total_auditado',
        'estado',
        'users_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleAuditoria::class, 'auditorias_id');
    }
}
