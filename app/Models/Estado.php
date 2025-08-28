<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'estados';

    protected $fillable = [
        'nome',
    ];

    // Relacionamentos inversos, se necessário
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'estado');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'estado');
    }

    public function entradaItens()
    {
        return $this->hasMany(EntradaItem::class, 'estado');
    }

    public function fornecedores()
    {
        return $this->hasMany(Fornecedor::class, 'estado');
    }

    public function tiposEntrada()
    {
        return $this->hasMany(TipoEntrada::class, 'estado');
    }
}