<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaEnvio extends Model
{
    use HasFactory;

    protected $table = 'tarifas_envios';

    protected $fillable = [
        'nombre',
        'costo',
        'activo',
    ];
}
