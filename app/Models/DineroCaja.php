<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Dinero que el administrador deja en caja en un día (una fila por fecha). */
class DineroCaja extends Model
{
    protected $table = 'dinero_caja';

    protected $fillable = ['fecha', 'monto', 'users_id'];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
