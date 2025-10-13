<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEntrada extends Model
{
    use HasFactory;

    protected $table = 'tipos_entradas';

    protected $fillable = [
        'nome',
        'descricao',
        'estado',
    ];

    public function estadoObj()
    {
        return $this->belongsTo(Estado::class, 'estado');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'tipo_entrada_id');
    }
}