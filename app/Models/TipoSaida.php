<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoSaida extends Model
{
    use HasFactory;

    protected $table = 'tipos_saidas';

    protected $fillable = [
        'nome',
        'descricao',
        'estado',
    ];

    public function estadoObj()
    {
        return $this->belongsTo(Estado::class, 'estado');
    }

    public function saidas()
    {
        return $this->hasMany(Saida::class, 'tipo_saida_id');
    }
}
